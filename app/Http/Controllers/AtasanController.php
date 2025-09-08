<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SKP;
use App\Models\Laporan;

use App\Models\Employees;
use App\Models\Activities;
use Illuminate\Support\Str;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AtasanController extends Controller
{
    public function index (){
        $user = Auth::user();

        return view('dashboard');
    }


    public function listTL(){
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $tl = TindakLanjut::latest()->whereNull('is_deleted')->where('followup_status', 2)->get();
        $laporan = Laporan::latest()->whereNull('is_deleted')->get(); 

        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/tindaklanjut/tindaklanjut', compact('tl','laporan'));
    }

    public function viewTL($laporan, Request $request){
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $report = TindakLanjut::findOrFail($laporan);
        $link_fileLaporan = Laporan::where('report_number', $report->report_number)
            ->whereNull('is_deleted')
            ->latest('created_at')
            ->first();

        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/tindaklanjut/create-tindaklanjut', compact('report', 'link_fileLaporan'));
    }

    public function updateTL (Request $request, $id){
        
        $validator = Validator::make($request->all(), [
            'tl_description' => 'required',
            'tl_document' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
       $tindakLanjut =  TindakLanjut::findOrFail($id);

        $data = [
            'tl_description'  => $request->tl_description,
            'followup_status'   => 3,
        ];


    if ($request->hasFile('tl_document')) {
        $file     = $request->file('tl_document');
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext      = $file->getClientOriginalExtension();
        $nameFile = Str::slug($original) . '-' . now()->format('YmdHis') . '.' . $ext;

        // Simpan ke storage/app/public/tindaklanjut/...
        $path = $file->storeAs('tindaklanjut', $nameFile, 'public');

        // Hapus file lama jika ada
        if ($tindakLanjut->tl_document && Storage::disk('public')->exists($tindakLanjut->tl_document)) {
            Storage::disk('public')->delete($tindakLanjut->tl_document);
        }

        // Set path baru ke kolom
        $data['tl_document'] = $path;
        }

         $tindakLanjut->update($data);

        // if (filled($tindakLanjut->phone_number_opd)) {
        // try {
        //     $this->sendTL($tindakLanjut->id);
        //     return back()->with('success', 'Data tersimpan & notifikasi WA terkirim.');
        // } catch (\Throwable $e) {
        //     report($e);
        //     return back()->with('warning', 'Data tersimpan, tetapi pengiriman WA gagal.');
        // }
        //  }


        return redirect()->back()->with('success' ,'Record updated successfully.');

    }

        public function sendTL ($id){
        
            $tl = TindakLanjut::findOrFail($id);

            // URL file bukti (jika ada)
            $evidenceUrl = $tl->tl_document
            ? Storage::disk('public')->url($tl->tl_document)
            : null;
            
            $to = $this->normalizeMsisdn($tl->phone_number_opd);


            $message = "*Pemberitahuan Tindak Lanjut*\n"
                    . "No Laporan: {$tl->report_number}\n"
                    . "Nama Laporan: {$tl->report_name}\n"
                    . "Deadline: " . Carbon::parse($tl->report_dateline)->format('d/m/Y'). "\n\n"
                    . "Mohon tindak lanjut dan konfirmasi penerimaan.\n"
                    . "Bukti kirim: ".($evidenceUrl ?: '-')."\n\n"
                    . "— Sistem Pengawasan";

            $payload = [
                'to'            => $to,
                'type'          => $evidenceUrl ? 'media' : 'text',
                'template'      => 'tl-followup-v1',
                'message'       => $message,
                'media_url'     => $evidenceUrl,
                'media_caption' => "Bukti tindak lanjut #{$tl->report_number}",
                'meta' => [
                    'laporan_id'      => $tl->id,
                    'dateline'        => Carbon::parse($tl->report_dateline)->format('d/m/Y'),
                    'idempotency_key' => "tl-{$tl->id}-".now()->format('YmdHis'),
                ],
                'callback_url' => config('services.wa.callback_url'),
            ];

            dd($payload);

            $resp = Http::withToken(config('services.wa.token'))
                ->acceptJson()
                ->asJson()
                ->withHeaders(['X-Idempotency-Key' => $payload['meta']['idempotency_key']])
                ->post(rtrim(config('services.wa.endpoint'), '/').'/api/wa/send', $payload);

            if ($resp->failed()) {
                report(new \Exception('WA send failed: '.$resp->body()));
                return back()->with('warning', 'Data tersimpan, tetapi pengiriman WA gagal.');
            }

            $result = $resp->json();
            
            Tindak_Lanjut_Log::Create([
                'user_id' => Auth::user()->id,
                'wa_status'     => $result['status'] ?? 'queued',
                'wa_message_id' => $result['message_id'] ?? null,
                'wa_sent_at'    => now(),
            ]);

            return back()->with('success', 'Data tersimpan & notifikasi WA terkirim.');

    }

    private function normalizeMsisdn(string $phone): string
{
    $p = preg_replace('/\D+/', '', $phone);
    if (\Illuminate\Support\Str::startsWith($p, '0'))  return '+62'.substr($p, 1);
    if (\Illuminate\Support\Str::startsWith($p, '62')) return '+'.$p;
    return \Illuminate\Support\Str::startsWith($phone, '+') ? $phone : '+'.$p;
}




    public function ApproveActivity(Request $request, $id){
                
        $activities = Activities::findOrFail($id);

       $activities->update([
            'status' => 1,
            'updated_at' => Carbon::now()
        ]);


        return redirect()->back()->with('success' ,'Sukses Approve Aktivitas');
        
    }

    public function listSKP(Request $request){
        $user = Auth::user();
        // if($request->input()){
        //     $year = $request->input('year');
        //     $query = SKP::whereNull('is_deleted')->where('created_by', $user->nip);
        //     if ($year != 'all') {
        //         $skp = $query->where('year', $year)->get();
        //     }
        //     else{
        //         $skp = $query->get();
        //     }   
        // }
        //     $skp = SKP::whereNull('is_deleted')->where('created_by', $user->nip)->get();

        $query = SKP::query();
        if ($request->has('year') && $request->year != '') {
            $query->where('year', $request->year);
        }

        $skp = $query->whereNull('is_deleted')->where('created_by', $user->nip)->get();

        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $availableYears = DB::table('skp')
        // ->selectRaw('YEAR(created_at) as year')
        ->distinct()
        // ->orderByRaw('YEAR(created_at)')
        ->pluck('year');
        return view('pegawai/skp', compact('skp','monthNames','availableYears'));
    }

    
    public function storeSKP (Request $request){
        
        $validator = Validator::make($request->all(), [
            'name_skp' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $employee = Employees::where('nip', Auth::user()->nip)->first();
        // $skpList = SKP::whereNull('is_deleted')->where('created_by', $employee->nip)->get();
        // return $skpList;
        // foreach ($skpList as $key) {
        //     return $key->month;
        // }
        $checkSKP = SKP::
        where('month', $request->month)
        ->where('year', $request->year)
        ->where('created_by', $employee->nip)
        ->first();

        if ($checkSKP) {
            return redirect()->back()->with('error' ,'Sudah input SKP di bulan dan tahun tersebut!');
        }


       $skp =  SKP::create([
            'employee_id' => $employee->id,
            'name_skp' => $request->name_skp,
            'month' => $request->month,
            'year' => $request->year,
            'created_at' => Carbon::now(),
            'created_by' => Auth::user()->nip
        ]);


        return redirect()->back()->with('success' ,'Record inserted successfully.');
    }


    public function updateSKP (Request $request, $id){
        
        $validator = Validator::make($request->all(), [
            'name_skp' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
       $skp =  SKP::findOrFail($id);

       $employee = Employees::where('nip', Auth::user()->nip)->first();
       $checkSKP = SKP::
       where('month', $request->month)
       ->where('year', $request->year)
       ->where('created_by', $employee->nip)
       ->first();

       if ($checkSKP) {
           return redirect()->back()->with('error' ,'Sudah ada SKP di bulan dan tahun tersebut!');
       }
       
       
       $skp->update([
            'name_skp' => $request->name_skp,
            'month' => $request->month,
            'year' => $request->year,
            'updated_at' => Carbon::now(),
        ]);


        return redirect()->back()->with('success' ,'Record updated successfully.');
    }

    public function softDeleteSKP($id){
        $skp = SKP::findOrFail($id);
        $skp->update([
            'is_deleted' => 1,
        ]);
        // $skp->delete();
        return redirect()->back()->with('success' ,'Record deleted successfully.');    
    }  


    public function listActivity(){
        $activities = Activities::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/activity', compact('activities', 'skp'));
    }

    
    public function storeActivity (Request $request){
        
        $validator = Validator::make($request->all(), [
            'skp_id' => 'required',
            'activity' => 'required',
            'description' => 'required',
            'start_time' => ['required', 'date_format:H:i', 'before:end_time', 'after_or_equal:08:00'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time', 'before_or_equal:17:00'],
        ],[
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Format jam mulai harus HH:MM.',
            'start_time.before' => 'Jam mulai harus sebelum jam selesai.',
            'start_time.after_or_equal' => 'Jam mulai minimal pukul 08:00.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.date_format' => 'Format jam selesai harus HH:MM.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'end_time.before_or_equal' => 'Jam selesai maksimal pukul 17:00.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $employee = Employees::findorFail(Auth::user()->id);
        // $time_now = Carbon::now();
        // $checkActivity = SKP::where('employee_id', $employee->id)
        // ->where('created_at', $request->created_at)
        // ->first();

        // if ($checkActivity) {
        //     return redirect()->back()->with('error' ,'');

        // }


       $activities =  Activities::create([
            'employee_id' => $employee->id,
            'skp_id' => $request->skp_id,
            'activity' => $request->activity,
            'description' => $request->description,
            'created_by' => Auth::user()->nip,
            'created_name' => Auth::user()->username,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'nip_atasan'=> $employee->nip_atasan,
            'created_at' => Carbon::now()
        ]);


        return redirect()->back()->with('success' ,'Record inserted successfully.');

    }


    public function updateActivity (Request $request, $id){
        
        $validator = Validator::make($request->all(), [
            'skp_id' => 'required',
            'activity' => 'required',
            'description' => 'required',
            'start_time' => ['required', 'before:end_time', 'after_or_equal:08:00'],
            'end_time'   => ['required', 'after:start_time', 'before_or_equal:17:00'],
        ],[
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.before' => 'Jam mulai harus sebelum jam selesai.',
            'start_time.after_or_equal' => 'Jam mulai minimal pukul 08:00.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'end_time.before_or_equal' => 'Jam selesai maksimal pukul 17:00.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
                
        $activities = Activities::findOrFail($id);

       $activities->update([
            'skp_id' => $request->skp_id,
            'activity' => $request->activity,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'updated_at' => Carbon::now()
        ]);


        return redirect()->back()->with('success' ,'Record updated successfully.');

    }

    public function softDeleteActivity ($id){
        $activity = Activities::findOrFail($id);
        $activity->update([
            'is_deleted' => 1,
        ]);
        // $acti->delete();
        return redirect()->back()->with('success' ,'Record deleted successfully.');    
    }  

    public function filterActivity(){

    }

    
    public function filterSKP(){

    }

    public function profile(){
        $employees = Employees::where('nip', Auth::user()->nip)->first();
        return view ('profile', compact('employees'));
    }


    public function listRecap(Request $request){
        
    $year = $request->input('year');


    $query = Activities::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
    ->groupByRaw('MONTH(created_at)')
    ->orderByRaw('MONTH(created_at)');

    if ($year) {
        $query->whereYear('created_at', $year);
    }

    $bulanAktivitas = $query->get();

    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April',   5 => 'Mei',      6 => 'Juni',
        7 => 'Juli',    8 => 'Agustus',  9 => 'September',
        10 => 'Oktober',11 => 'November',12 => 'Desember'
    ];


    $availableYears = DB::table('activities')
    ->selectRaw('YEAR(created_at) as year')
    ->distinct()
    ->orderByRaw('YEAR(created_at)')
    ->pluck('year');

    return view('pegawai.rekap', compact('bulanAktivitas', 'monthNames','availableYears'));
}

public function ExcelRecap(Request $request){
            $month = $request->month;

            $activities = Activities::where('created_by', Auth::user()->nip)->whereMonth('created_at', $month)->get();
            // $activities = Activity::whereMonth('created_at', $request->month)->get();

    
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Nama Aktivitas');
            $sheet->setCellValue('C1', 'Deskripsi');
            $sheet->setCellValue('D1', 'Tanggal');
    
            $rowNumber = 2; 
            $no = 1;
            foreach ($activities as $activity) {
                $sheet->setCellValue('A' . $rowNumber, $no++);
                $sheet->setCellValue('B' . $rowNumber, $activity->activity);
                $sheet->setCellValue('C' . $rowNumber, $activity->description);
                $sheet->setCellValue('D' . $rowNumber, $activity->created_at->format('Y-m-d H:i:s'));
    
                $rowNumber++;
            }
    
            $writer = new Xlsx($spreadsheet);
    
            $fileName = 'rekap_aktivitas.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$fileName}\"");
            header('Cache-Control: max-age=0');
    
            $writer->save('php://output');
            exit; 

}

    

}


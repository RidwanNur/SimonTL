<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SKP;
use App\Models\Laporan;
use App\Models\Employees;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PegawaiController extends Controller
{
    public function index () {
        $user = Auth::user();
        // if($user->is_atasan == 1){
        //     $query_get_bawahan = "SELECT COUNT(*) AS TOTAL FROM employees WHERE NIP_ATASAN = '{$user->nip}'";
        // }else {
        //     $query_get_bawahan = "SELECT 0 AS TOTAL";
        // }
        // $query_activities = "SELECT COUNT(*) AS TOTAL FROM activities LEFT OUTER JOIN employees ON activities.EMPLOYEE_ID = employees.ID WHERE employees.NIP = '{$user->nip}'";
        // $query_activities_approve = "SELECT COUNT(*) AS TOTAL FROM activities LEFT OUTER JOIN employees ON activities.EMPLOYEE_ID = employees.ID WHERE employees.NIP = '{$user->nip}' AND STATUS IS NOT NULL";
        // $query_activities_delay = "SELECT COUNT(*) AS TOTAL FROM activities LEFT OUTER JOIN employees ON activities.EMPLOYEE_ID = employees.ID WHERE employees.NIP = '{$user->nip}' AND STATUS IS NULL";

        // $get_bawahan = DB::select($query_get_bawahan);
        // $get_activities = DB::select($query_activities);
        // $get_activities_delay = DB::select($query_activities_delay);
        // $get_activities_approve = DB::select($query_activities_approve);
        return view('dashboard');
    }

    public function listTL(){
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $laporan = Laporan::latest()->whereNull('is_deleted')->get();
        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/tindaklanjut/tindaklanjut', compact('laporan'));
    }

        public function viewTL($laporan, Request $request){
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $report = Laporan::findOrFail($laporan);
        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/tindaklanjut/create-tindaklanjut', compact('report'));
    }

    

    public function updateTL (Request $request, $id){
        
        $validator = Validator::make($request->all(), [
            'report_deadline' => 'required',
            'phone_number_opd' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
       $tindakLanjut =  Laporan::findOrFail($id);

       
       $tindakLanjut->update([
            'report_deadline' => $request->report_deadline,
            'phone_number_opd' => $request->phone_number_opd,
            'followup_status' => 'Proses',
            'sending_evidence' => $path,
            'updated_at' => Carbon::now()
        ]);


        return redirect()->back()->with('success' ,'Record updated successfully.');

    }

    public function softDeleteSKP($id){
        $skp = SKP::findOrFail($id);
        $skp->update([
            'is_deleted' => 1,
        ]);

        return redirect()->back()->with('success' ,'Record deleted successfully.');
    }  

     public function getFileTokenAttribute(): string
    {
        return strtr(Crypt::encryptString((string) $this->getKey()), '+/', '-_');
    }


    public function listActivity(){
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $laporan = Laporan::latest()->whereNull('is_deleted')->get();
        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/laporan', compact('laporan'));
    }

    
    public function storeActivity (Request $request){
        
        $validator = Validator::make($request->all(), [
            'report_number' => 'required',
            'report_name' => 'required',
            'report_date' => 'required',
            'team_lead'   => 'required',
            'phone_number_teamlead'   => 'required',
            'file_name' => 'required|file|max:5600|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $report = Laporan::where('report_number',$request->report_number)->first();
        if ($report) {
            return redirect()->back()->with('error' ,'Data Laporan sudah ada!');
            }

        $file = $request->file('file_name');
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $nameFile = Str::slug($original).'-'.now()->format('YmdHis').'.'.$ext;

        $path = $file->storeAs('laporan', $nameFile, 'public');
        
       $report =  Laporan::create([
            'user_id' => Auth::user()->id,
            'report_number' => $request->report_number,
            'report_name' => $request->report_name,
            'report_date' => $request->report_date,
            'team_lead' => $request->team_lead,
            'phone_number_teamlead' => $request->phone_number_teamlead,
            'link_file' => $path,
            'created_at' => Carbon::now()
        ]);


        return redirect()->back()->with('success' ,'Record inserted successfully.');

    }


    public function reportFile(Laporan $laporan){


    if (! $laporan->link_file || ! Storage::disk('public')->exists($laporan->link_file)) {
        abort(404, 'File tidak ditemukan.');
    }

    return Storage::disk('public')->response($laporan->link_file);
    }


    public function updateActivity (Request $request, $id){
                
     $report = Laporan::findOrFail($id);

     $validated = $request->validate([
        'report_number'          => 'required|string|max:100',
        'report_name'            => 'required|string|max:255',
        'report_date'            => 'required|date',
        'team_lead'              => 'nullable|string|max:255',
        'phone_number_teamlead'  => 'required|string|max:30',
        'file_name_edit'         => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
    ]);




    dd($request->file('file_name_edit'));

        $path = $report->link_file;

   
        if($file = $request->file('file_name_edit')){     
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext      = $file->getClientOriginalExtension();
        $nameFile = Str::slug($original) . '-' . now()->format('YmdHis') . '.' . $ext;

        $path = $file->storeAs('laporan', $nameFile, 'public');

        if ($report->link_file && Storage::disk('public')->exists($report->link_file)) {
            Storage::disk('public')->delete($report->link_file);
        }
    }
    


    $report->update([
        'user_id'               => Auth::user()->id,
        'report_number'         => $validated['report_number'],
        'report_name'           => $validated['report_name'],
        'report_date'           => $validated['report_date'],
        'team_lead'             => $validated['team_lead'] ?? null,
        'phone_number_teamlead' => $validated['phone_number_teamlead'],
        'link_file'             => $path,
        'updated_at'            => Carbon::now(),
    ]);


        return redirect()->back()->with('success' ,'Record updated successfully.');

    }

    public function softDeleteActivity ($id){
        $activity = Laporan::findOrFail($id);
        $activity->update([
            'is_deleted' => 1,
        ]);
        // $acti->delete();
        return redirect()->back()->with('success' ,'Record deleted successfully.');    
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

    public function profile(){
        $employees = Employees::where('nip', Auth::user()->nip)->first();
        return view ('profile', compact('employees'));
    }



}

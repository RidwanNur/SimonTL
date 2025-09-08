<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SKP;
use App\Models\Laporan;
use App\Models\Employees;
use App\Models\Tindak_Lanjut_Log;
use App\Models\TindakLanjut;
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
    public function index()
    {
        $user = Auth::user();
        return view('dashboard');
    }

    public function listTL()
    {
        $userId = Auth::user()->id;
        $tl = TindakLanjut::latest()->whereNull('is_deleted')->where('followup_status', 1)->whereNull('assign_to')->get();
        $tl_verifikasi = TindakLanjut::query()->where('followup_status', 4)->where('assign_to', $userId)->latest()->get();
        $laporan = Laporan::query()
            ->whereNull('is_deleted')            
            ->whereNotIn('report_number', function ($q) {
                $q->select('report_number')->from('tindak_lanjut');
            })
            ->latest()
            ->get();

        return view('pegawai/tindaklanjut/tindaklanjut', compact('tl', 'laporan', 'tl_verifikasi'));
    }

    public function viewTL($laporan, Request $request)
    {
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $report = TindakLanjut::findOrFail($laporan);
        $link_fileLaporan = Laporan::where('report_number', $report->report_number)
            ->whereNull('is_deleted')
            ->latest('created_at')
            ->first();

        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/tindaklanjut/create-tindaklanjut', compact('report', 'link_fileLaporan'));
    }

    public function storeTL(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'report_dateline' => 'required',
            'phone_number_opd' => 'required',
            'report_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $laporan = Laporan::where('id', $request->report_number)->first();

        TindakLanjut::Create([
            'user_id' => Auth::user()->id,
            'report_dateline' => $request->report_dateline,
            'phone_number_opd' => $request->phone_number_opd,
            'report_number' => $laporan->report_number,
            'report_name' => $laporan->report_name,
            'report_date' => $laporan->report_date,
            'team_lead' => $laporan->team_lead,
            'followup_status' => 1,
            'updated_at' => Carbon::now()
        ]);


        return redirect()->back()->with('success', 'Record updated successfully.');
    }



    public function updateTL(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'report_dateline' => 'required',
            'phone_number_opd' => 'required',
            'sending_evidence' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tindakLanjut =  TindakLanjut::findOrFail($id);

        $data = [
            'report_dateline'   => $request->report_dateline,
            'phone_number_opd'  => $request->phone_number_opd,
            'followup_status' => 2 //optional setelah notif wa sukses hapus
        ];


        if ($request->hasFile('sending_evidence')) {
            $file     = $request->file('sending_evidence');
            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext      = $file->getClientOriginalExtension();
            $nameFile = Str::slug($original) . '-' . now()->format('YmdHis') . '.' . $ext;

            // Simpan ke storage/app/public/tindaklanjut/...
            $path = $file->storeAs('buktiKirim', $nameFile, 'public');

            // Hapus file lama jika ada
            if ($tindakLanjut->sending_evidence && Storage::disk('public')->exists($tindakLanjut->sending_evidence)) {
                Storage::disk('public')->delete($tindakLanjut->sending_evidence);
            }

            // Set path baru ke kolom
            $data['sending_evidence'] = $path;
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


        return redirect()->back()->with('success', 'Record updated successfully.');
    }


    public function sendTL($id)
    {

        $tl = TindakLanjut::findOrFail($id);

        // URL file bukti (jika ada)
        $evidenceUrl = $tl->sending_evidence
            ? Storage::disk('public')->url($tl->sending_evidence)
            : null;

        $to = $this->normalizeMsisdn($tl->phone_number_opd);


        $message = "*Pemberitahuan Tindak Lanjut*\n"
            . "No Laporan: {$tl->report_number}\n"
            . "Nama Laporan: {$tl->report_name}\n"
            . "Deadline: " . Carbon::parse($tl->report_dateline)->format('d/m/Y') . "\n\n"
            . "Mohon tindak lanjut dan konfirmasi penerimaan.\n"
            . "Bukti kirim: " . ($evidenceUrl ?: '-') . "\n\n"
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
                'idempotency_key' => "tl-{$tl->id}-" . now()->format('YmdHis'),
            ],
            'callback_url' => config('services.wa.callback_url'),
        ];
        dd($payload);

        $resp = Http::withToken(config('services.wa.token'))
            ->acceptJson()
            ->asJson()
            ->withHeaders(['X-Idempotency-Key' => $payload['meta']['idempotency_key']])
            ->post(rtrim(config('services.wa.endpoint'), '/') . '/api/wa/send', $payload);

        if ($resp->failed()) {
            report(new \Exception('WA send failed: ' . $resp->body()));
            return back()->with('warning', 'Data tersimpan, tetapi pengiriman WA gagal.');
        }

        $result = $resp->json();

        $tl->update([
            'followup_status' => 2
        ]);
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
        if (\Illuminate\Support\Str::startsWith($p, '0'))  return '+62' . substr($p, 1);
        if (\Illuminate\Support\Str::startsWith($p, '62')) return '+' . $p;
        return \Illuminate\Support\Str::startsWith($phone, '+') ? $phone : '+' . $p;
    }

    public function softDeleteTL($id)
    {
        $activity = TindakLanjut::findOrFail($id);
        $activity->update([
            'is_deleted' => 1,
        ]);
        // $acti->delete();
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }



    public function softDeleteSKP($id)
    {
        $skp = SKP::findOrFail($id);
        $skp->update([
            'is_deleted' => 1,
        ]);

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

    public function getFileTokenAttribute(): string
    {
        return strtr(Crypt::encryptString((string) $this->getKey()), '+/', '-_');
    }


    public function listActivity()
    {
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $laporan = Laporan::latest()->whereNull('is_deleted')->get();
        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/laporan', compact('laporan'));
    }


    public function storeActivity(Request $request)
    {

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
        $report = Laporan::where('report_number', $request->report_number)->first();
        if ($report) {
            return redirect()->back()->with('error', 'Data Laporan sudah ada!');
        }

        $file = $request->file('file_name');
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $nameFile = Str::slug($original) . '-' . now()->format('YmdHis') . '.' . $ext;

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


        return redirect()->back()->with('success', 'Record inserted successfully.');
    }


    public function reportFile(Laporan $laporan)
    {


        if (! $laporan->link_file || ! Storage::disk('public')->exists($laporan->link_file)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->response($laporan->link_file);
    }


    public function updateActivity(Request $request, $id)
    {

        $report = Laporan::findOrFail($id);

        $validated = $request->validate([
            'report_number'          => 'required|string|max:100',
            'report_name'            => 'required|string|max:255',
            'report_date'            => 'required|date',
            'team_lead'              => 'nullable|string|max:255',
            'phone_number_teamlead'  => 'required|string|max:30',
            'file_name_edit'         => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);


        $path = $report->link_file;


        if ($file = $request->file('file_name_edit')) {
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


        return redirect()->back()->with('success', 'Record updated successfully.');
    }

    public function softDeleteActivity($id)
    {
        $activity = Laporan::findOrFail($id);
        $activity->update([
            'is_deleted' => 1,
        ]);
        // $acti->delete();
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }



    public function listRecap(Request $request)
    {

        $year = $request->input('year');


        $query = Activities::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)');

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $bulanAktivitas = $query->get();

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
            12 => 'Desember'
        ];


        $availableYears = DB::table('activities')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByRaw('YEAR(created_at)')
            ->pluck('year');

        return view('pegawai.rekap', compact('bulanAktivitas', 'monthNames', 'availableYears'));
    }

    public function ExcelRecap(Request $request)
    {
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

    public function profile()
    {
        $employees = Employees::where('nip', Auth::user()->nip)->first();
        return view('profile', compact('employees'));
    }
}

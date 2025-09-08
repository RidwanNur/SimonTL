<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Laporan;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use App\Models\MasterInstansi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class AdminController extends Controller
{
    public function index () {
        // $employee = "SELECT COUNT(*) AS TOTAL FROM employees"; 
        // $query_activities_approve = "SELECT COUNT(*) AS TOTAL FROM activities WHERE STATUS IS NOT NULL";
        // $query_activities_delay = "SELECT COUNT(*) AS TOTAL FROM activities WHERE STATUS IS NULL";
        // $query_last_activity = "SELECT activities.CREATED_AT AS TANGGAL, employees.NIP, activities.CREATED_NAME AS NAMA_PEGAWAI, activities.ACTIVITY, activities.DESCRIPTION FROM activities LEFT OUTER JOIN employees ON activities.EMPLOYEE_ID = employees.ID ORDER BY activities.CREATED_AT DESC LIMIT 5";


        // $total_employee = DB::select($employee);
        // $total_activity_appr = DB::select($query_activities_approve);
        // $total_activity_delay = DB::select($query_activities_delay);
        // $last_activity = DB::select($query_last_activity);

        return view('dashboard');
    }

    public function listEmployee (){
            $users = User::where('status', 1)->WhereNull('is_deleted')->get();
            $opds = MasterInstansi::all();
            // $atasan = Employees::whereNotNull('region')->whereNull('is_deleted')->get();
            return view('admin.pegawai', compact('users', 'opds'));   
        
    }

        public function getInstansi($instansi)
        {
            $instansi  = MasterInstansi::all();

            return response()->json($instansi);
        }

    public function storeUser (Request $request){
        
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'instansi' => 'required',
            'no_hp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $checkUser = User::where('username', $request->username)->first();
        if ($checkUser) {
            return redirect()->back()->with([
                'code' => 409,
                'status' => 'Error',
                'message' => 'User Sudah Ada!'
            ], 409);
        }

        
        if($request->instansi == 'BPKP'){
        $user = User::create([
            'username' => $request->username,
            'instansi' => $request->instansi,
            'password' => Hash::make('123456'),
            'no_hp' => $request->no_hp,
            'status' => 1,
            'created_at' => Carbon::now(),
        ]);

        $user->assignRole('pegawai');
            
        }
        else {
            $user = User::create([
            'username' => $request->username,
            'instansi' => $request->instansi,
            'password' => Hash::make('123456'),
            'no_hp' => $request->no_hp,
            'status' => 1,
            'created_at' => Carbon::now(),
       
        ]);
        $user->assignRole('opd');
        }

        return redirect()->back()->with('success' ,'Record inserted successfully.');

    }

    public function softDeleteEmployee($id){
        $user = User::findOrFail($id);
        // return $user;
        $user->update([
            'is_deleted' => 1,
        ]);


        return redirect()->back()->with('success' ,'Record deleted successfully.');
    }  

    public function updateEmployee(Request $request, $id){

            $validator = Validator::make($request->all(), [
            'username' => 'required',
            'instansi' => 'required',
            'no_hp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $users = User::findOrFail($id);
        $userUpdate = [
            'username' => $request->username,
            'instansi' => $request->instansi,
            'no_hp' => $request->no_hp,
            'updated_at' => Carbon::now()
        ];

        $users->update($userUpdate);
        return redirect()->back()->with('success' ,'Record updated successfully.');

    }


        public function listTL(){
        // $laporan = Laporan::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->with('skp')->get();
        $tl = TindakLanjut::latest()->whereNull('is_deleted')->where('followup_status', 3)->get();
        $pegawai = User::role('pegawai')->orderBy('username')->get(['id','username']);
        $laporan = Laporan::latest()->whereNull('is_deleted')->get(); 

        // $skp = SKP::whereNull('is_deleted')->where('created_by', Auth::user()->nip)->get();
        return view('pegawai/tindaklanjut/tindaklanjut', compact('tl','laporan','pegawai'));
    }

    
    public function updateTL (Request $request, $id){
        
        $validator = Validator::make($request->all(), [
            'assign_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
       $tindakLanjut =  TindakLanjut::findOrFail($id);

        $data = [
            'assign_to'  => $request->assign_to,
            'followup_status'   => 4,
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
    
        return view('admin.rekap', compact('bulanAktivitas', 'monthNames','availableYears'));
    }
    
    public function ExcelRecap(Request $request){
                $month = $request->month;
    
                $activities = Activities::whereMonth('created_at', $month)->get();
    
        
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



    public function settingApprover(){
        $employees = Employees::whereNull('is_deleted')->get();
        return view ('admin.set-approver', compact('employees'));
    }

    public function insertApprover(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_atasan_id' => 'required',
            'employee_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $atasan = Employees::where('id', $request->employee_atasan_id);
        $bawahan = Employees::where('id', $request->employee_id);

        $checkAtasanBawahan = Approver::where('employee_atasan_id', $atasan->id)
        ->where('employee_id', $bawahan->id)
        ->first();

        if ($checkAtasanBawahan) {
            return redirect()->back()->with([
                'code' => 409,
                'status' => 'Error',
                'message' => 'Approver sudah ada!'
            ], 409);
        }

       $approver =  Approver::create([
            'employee_atasan_id' => $atasan->id,
            'employee_id' => $bawahan->id,
            'nip_atasan' => $atasan->nip,
            'nip' => $bawahan->nip,
            'position_atasan' => $atasan->position,
            'position' => $bawahan->position,
            'nama' => $bawahan->name,
            'nama_atasan' => $atasan->name,
            'created_at' => Carbon::now,
        ]);

        return redirect()->back()->with(['message' => 'Record inserted successfully.', 'data' => $approver]);
    }


        public function updateApprover(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'employee_atasan_id' => 'required',
            'employee_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // $atasan = Approver::where('id', $request->employee_atasan_id);
        // $bawahan = Employees::where('id', $request->employee_id);

        $existingApprover = Approver::where('employee_atasan_id', $request->employee_atasan_id)
        ->where('employee_id', $request->employee_id)
        ->first();

        $approver = Approver::findOrFail($id);
       
        $approver->update([
            'nip_atasan' => $existingApprover->nip,
            'nip' => $existingApprover->nip,
            'position_exist$existingApprover' => $existingApprover->position,
            'position' => $existingApprover->position,
            'nama' => $existingApprover->name,
            'nama_atasan' => $existingApprover->name,
            'updated_at' => Carbon::now,
        ]);

        return redirect()->back()->with(['message' => 'Record updated successfully.', 'data' => $approver]);
    }

    public function profile(){
        $employees = Employees::where('nip', Auth::user()->nip)->first();
        return view ('profile', compact('employees'));
    }

    public function getReportActivity(){

    }

  

}

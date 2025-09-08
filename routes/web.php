<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return redirect('/login');
});

// Route::middleware(['role:admin'])->group(function () {
//     Route::get('/manage-books', [BookController::class, 'index'])->name('manage.books');
// });


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth','verified')->group(function () {
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/user', [AdminController::class, 'listEmployee'])->name('admin.listEmployee');
    Route::post('/user/create', [AdminController::class, 'storeUser'])->name('admin.storeUser');
    Route::put('/user/update/{id}', [AdminController::class, 'updateEmployee'])->name('admin.updateEmployee');
    Route::put('/user/delete/{id}', [AdminController::class, 'softDeleteEmployee'])->name('admin.softDeleteEmployee');
    Route::get('/assignTL', [AdminController::class, 'listTL'])->name('admin.listTL');
    Route::put('/assignTL/update/{id}', [AdminController::class, 'updateTL'])->name('admin.updateTL');
    Route::get('/tindakLanjut/edit/{laporan}', [PegawaiController::class, 'viewTL'])->name('pegawai.viewTL');
    Route::put('/tindakLanjut/update/{id}', [AtasanController::class, 'updateTL'])->name('opd.updateTL');
    Route::get('/rekap', [AdminController::class, 'listRecap'])->name('admin.listRecap');
    Route::get('/rekap/excel/{month}', [AdminController::class, 'ExcelRecap'])->name('admin.ExcelRecap');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile.view');
    Route::get('/get-instansi/{instansi}', [AdminController::class, 'getInstansi'])->name('admin.getInstansi');

});
});


Route::middleware('auth','verified')->group(function () {
    Route::middleware(['role:pegawai'])->prefix('pegawai')->group(function () {
    Route::get('/dashboard', [PegawaiController::class, 'index'])->name('pegawai.dashboard');
    Route::get('/laporan', [PegawaiController::class, 'listActivity'])->name('pegawai.listActivity');
    Route::get('/laporan/{laporan}/file', [PegawaiController::class, 'reportFile'])->name('pegawai.reportFile');
    // Route::get('/laporan/{token}/file', [PegawaiController::class, 'reportFile'])->name('pegawai.reportFile');
    Route::post('/laporan/create', [PegawaiController::class, 'storeActivity'])->name('pegawai.storeActivity');
    Route::put('/aktivitas/update/{id}', [PegawaiController::class, 'updateActivity'])->name('pegawai.updateActivity');
    Route::put('/aktivitas/delete/{id}', [PegawaiController::class, 'softDeleteActivity'])->name('pegawai.softDeleteActivity');
    Route::get('/aktivitas/filter', [PegawaiController::class, 'storeActivity'])->name('pegawai.filterActivity');
    Route::get('/tindakLanjut', [PegawaiController::class, 'listTL'])->name('pegawai.listTL');
    Route::post('/tindakLanjut/create', [PegawaiController::class, 'storeTL'])->name('pegawai.storeTindakLanjut');
    Route::get('/tindakLanjut/edit/{laporan}', [PegawaiController::class, 'viewTL'])->name('pegawai.viewTL');
    Route::put('/tindakLanjut/update/{id}', [PegawaiController::class, 'updateTL'])->name('pegawai.updateTL');
    Route::post('/tindakLanjut/send/{id}', [PegawaiController::class, 'sendTL'])->name('pegawai.sendTL');
    Route::put('/tindaklanjut/delete/{id}', [PegawaiController::class, 'softDeleteTL'])->name('pegawai.softDeleteTL');
    Route::put('/skp/delete/{id}', [PegawaiController::class, 'softDeleteSKP'])->name('pegawai.softDeleteSKP');
    Route::get('/skp/filter', [PegawaiController::class, 'filterSKP'])->name('pegawai.filterSKP');
    Route::get('/rekap', [PegawaiController::class, 'listRecap'])->name('pegawai.listRecap');
    Route::get('/rekap/excel/{month}', [PegawaiController::class, 'ExcelRecap'])->name('pegawai.ExcelRecap');
    Route::get('/profile', [PegawaiController::class, 'profile'])->name('profile.view');
});
});

Route::middleware('auth','verified')->group(function () {
    Route::middleware(['role:opd'])->prefix('opd')->group(function () {
    Route::get('/dashboard', [AtasanController::class, 'index'])->name('atasan.dashboard');
    Route::get('/tindakLanjut', [AtasanController::class, 'listTL'])->name('opd.listTL');
    Route::get('/tindakLanjut/edit/{laporan}', [AtasanController::class, 'viewTL'])->name('opd.viewTL');
    Route::put('/tindakLanjut/update/{id}', [AtasanController::class, 'updateTL'])->name('opd.updateTL');
    // Route::get('/tindakLanjut/edit/{laporan}', [AtasanController::class, 'viewTL'])->name('opd.viewTL');
    // Route::post('/tindakLanjut/send/{id}', [PegawaiController::class, 'sendTL'])->name('opd.sendTL');
    Route::get('/approval', [AtasanController::class, 'listApproval'])->name('atasan.listApproval');
    Route::get('/approval-filter', [AtasanController::class, 'filterListApprActivity'])->name('atasan.filterListApprActivity');
    Route::get('/approval/view', [AtasanController::class, 'viewOneActivity'])->name('atasan.viewOneActivity');
    Route::put('/approval/approve/{id}', [AtasanController::class, 'ApproveActivity'])->name('atasan.ApproveActivity');
    Route::get('/aktivitas', [AtasanController::class, 'listActivity'])->name('atasan.listActivity');
    Route::post('/aktivitas/create', [AtasanController::class, 'storeActivity'])->name('atasan.storeActivity');
    Route::put('/aktivitas/update/{id}', [AtasanController::class, 'updateActivity'])->name('atasan.updateActivity');
    Route::put('/aktivitas/delete/{id}', [AtasanController::class, 'softDeleteActivity'])->name('atasan.softDeleteActivity');
    Route::get('/aktivitas-filter', [AtasanController::class, 'storeActivity'])->name('atasan.filterActivity');
    Route::get('/skp', [AtasanController::class, 'listSKP'])->name('atasan.listSKP');
    Route::post('/skp/create', [AtasanController::class, 'storeSKP'])->name('atasan.storeSKP');
    Route::put('/skp/update/{id}', [AtasanController::class, 'updateSKP'])->name('atasan.updateSKP');
    Route::put('/skp/delete/{id}', [AtasanController::class, 'softDeleteSKP'])->name('atasan.softDeleteSKP');
    Route::get('/skp-filter', [AtasanController::class, 'filterSKP'])->name('atasan.filterSKP');
    Route::get('/rekap', [AtasanController::class, 'listRecap'])->name('atasan.listRecap');
    Route::get('/rekap/excel/{month}', [AtasanController::class, 'ExcelRecap'])->name('atasan.ExcelRecap');
    Route::get('/profile', [AtasanController::class, 'profile'])->name('profile.view');
});
});


Route::middleware('auth','verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
//         ->name('logout');

require __DIR__.'/auth.php';

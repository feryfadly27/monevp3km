<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KegiatanDetailController;
use App\Http\Controllers\SkemaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\RekapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->route('dashboard'); });

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profil
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin: kegiatan CRUD + import
    Route::middleware('role:admin')->group(function () {
        Route::get('/kegiatan/import',          [KegiatanController::class, 'importForm'])->name('kegiatan.import');
        Route::post('/kegiatan/import',         [KegiatanController::class, 'import'])->name('kegiatan.import.post');
        Route::get('/kegiatan/import/template', [KegiatanController::class, 'importTemplate'])->name('kegiatan.import.template');
        Route::get('/kegiatan/trash',            [KegiatanController::class, 'trash'])->name('kegiatan.trash');
        Route::post('/kegiatan/{id}/restore',    [KegiatanController::class, 'restore'])->name('kegiatan.restore');
        Route::delete('/kegiatan/{id}/force',    [KegiatanController::class, 'forceDelete'])->name('kegiatan.force-delete');
        Route::resource('kegiatan', KegiatanController::class)->except(['show']);

        // Detail write actions
        Route::post('/kegiatan/{kegiatan}/status',            [KegiatanDetailController::class, 'ubahStatus'])->name('kegiatan.status');
        Route::post('/kegiatan/{kegiatan}/anggota',           [KegiatanDetailController::class, 'tambahAnggota'])->name('kegiatan.anggota.tambah');
        Route::delete('/kegiatan/{kegiatan}/anggota/{dosen}', [KegiatanDetailController::class, 'hapusAnggota'])->name('kegiatan.anggota.hapus');
        Route::post('/kegiatan/{kegiatan}/berkas',            [KegiatanDetailController::class, 'uploadBerkas'])->name('kegiatan.berkas.upload');
        Route::delete('/kegiatan/{kegiatan}/berkas/{berkas}', [KegiatanDetailController::class, 'hapusBerkas'])->name('kegiatan.berkas.hapus');
        Route::post('/kegiatan/{kegiatan}/luaran',            [KegiatanDetailController::class, 'tambahLuaran'])->name('kegiatan.luaran.tambah');
        Route::delete('/kegiatan/{kegiatan}/luaran/{luaran}', [KegiatanDetailController::class, 'hapusLuaran'])->name('kegiatan.luaran.hapus');

        // Skema
        Route::resource('skema', SkemaController::class)->except(['show']);

        // Assign reviewer + Data
        Route::get('/reviewer/assign', fn() => view('reviewer.assign'))->name('reviewer.assign');
        Route::get('/dosen/import',          [DosenController::class, 'importForm'])->name('dosen.import');
        Route::post('/dosen/import',         [DosenController::class, 'import'])->name('dosen.import.post');
        Route::get('/dosen/import/template', [DosenController::class, 'importTemplate'])->name('dosen.import.template');
        Route::resource('dosen', DosenController::class)->except(['show']);
        Route::get('/users/import',          [UserController::class, 'importForm'])->name('users.import');
        Route::post('/users/import',         [UserController::class, 'import'])->name('users.import.post');
        Route::get('/users/import/template', [UserController::class, 'importTemplate'])->name('users.import.template');
        Route::resource('users', UserController::class)->except(['show']);

        // Rekap & Settings
        Route::get('/rekap',        [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/rekap/export', [RekapController::class, 'export'])->name('rekap.export');
        Route::get('/settings',     [SettingsController::class, 'index'])->name('settings.index');
    });

    // Admin + Reviewer: daftar kegiatan (read)
    Route::middleware('role:admin|reviewer')->group(function () {
        Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
    });

    // Semua role: lihat detail kegiatan (harus setelah semua route static /kegiatan/*)
    Route::get('/kegiatan/{kegiatan}', [KegiatanDetailController::class, 'show'])->name('kegiatan.show');

    // Reviewer: penilaian
    Route::middleware('role:reviewer')->group(function () {
        Route::get('/tugas',                     [PenilaianController::class, 'tugas'])->name('tugas.index');
        Route::get('/penilaian/{penugasan}',     [PenilaianController::class, 'form'])->name('penilaian.form');
        Route::post('/penilaian/{penugasan}',    [PenilaianController::class, 'simpan'])->name('penilaian.simpan');
    });

    // Dosen: kegiatan saya
    Route::middleware('role:dosen')->group(function () {
        Route::get('/kegiatan-saya', [PenilaianController::class, 'kegiatanSaya'])->name('kegiatan-saya.index');
    });
});

require __DIR__.'/auth.php';

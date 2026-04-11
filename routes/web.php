<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Guru\LaporanAbsensiController;
use App\Http\Controllers\Guru\ExportAbsensiController;

Auth::routes();

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'throttle:global'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {

        // Guru Management
        Route::resource('guru', GuruController::class);

        // Kelas Management
        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::post('kelas/{kelas}/siswa', [KelasController::class, 'addSiswa'])->name('kelas.siswa.add');
        Route::put('kelas/{kelas}/siswa/{siswa}', [KelasController::class, 'updateSiswa'])->name('kelas.siswa.update');
        Route::delete('kelas/{kelas}/siswa/{siswa}', [KelasController::class, 'destroySiswa'])->name('kelas.siswa.destroy');

        // Mata Pelajaran
        Route::resource('mapel', MataPelajaranController::class);


        // Jadwal Management
        Route::resource('jadwal', JadwalController::class);
        Route::get('jadwal/kelas/{kelas}', [JadwalController::class, 'byKelas'])
            ->name('jadwal.by-kelas');
    });

    // Guru Routes
    Route::prefix('guru')->name('guru.')->group(function () {

        // Absensi Management (menggunakan AbsensiController)
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/jadwal/{jadwal}/pertemuan/create', [AbsensiController::class, 'createPertemuan'])->name('absensi.pertemuan.create');
        Route::post('absensi/jadwal/{jadwal}/pertemuan', [AbsensiController::class, 'storePertemuan'])->name('absensi.pertemuan.store');
        Route::get('absensi/pertemuan/{pertemuan}', [AbsensiController::class, 'inputAbsensi'])->name('absensi.input');
        Route::put('absensi/pertemuan/{pertemuan}', [AbsensiController::class, 'updateAbsensi'])->name('absensi.update');

        // Laporan (menggunakan LaporanAbsensiController)
        Route::get('laporan/harian', [LaporanAbsensiController::class, 'harian'])->name('laporan.harian');
        Route::get('laporan/bulanan', [LaporanAbsensiController::class, 'bulanan'])->name('laporan.bulanan');
        Route::get('laporan/semester', [LaporanAbsensiController::class, 'semester'])->name('laporan.semester');

        // Export PDF (menggunakan ExportAbsensiController)
        Route::get('laporan/harian/pdf', [ExportAbsensiController::class, 'harian'])->name('laporan.harian.pdf');
        Route::get('laporan/bulanan/pdf', [ExportAbsensiController::class, 'bulanan'])->name('laporan.bulanan.pdf');
        Route::get('laporan/semester/pdf', [ExportAbsensiController::class, 'semester'])->name('laporan.semester.pdf');
    });
});



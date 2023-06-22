<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AktivitasMahasiswaController;
use App\Http\Controllers\BimbinganMahasiswa;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\SandboxController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\LogImportController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ReferensiController;
use App\Http\Controllers\MataKuliahController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [SettingController::class, 'index']);
Route::get('setting', [SettingController::class, 'index']);
Route::post('check-setting', [SettingController::class, 'checkSetting']);

Route::middleware('authneofeeder')->group(function() {
    Route::prefix('dashboard')->group(function () {
        Route::middleware('sandbox')->group(function() {
            Route::get('profil', [ProfilController::class, 'index']);

            Route::get('mahasiswa', [MahasiswaController::class, 'index']);
            Route::post('mahasiswa', [MahasiswaController::class, 'store']);

            Route::get('mata-kuliah', [MataKuliahController::class, 'index']);
            Route::post('mata-kuliah', [MataKuliahController::class, 'store']);

            Route::get('kurikulum', [KurikulumController::class, 'index']);
            Route::post('kurikulum', [KurikulumController::class, 'store']);
            Route::get('kurikulum/{id}', [KurikulumController::class, 'show']);
            Route::post('kurikulum-matkul/{id}', [KurikulumController::class, 'storeMatkul']);

            // Route::get('kelas', [KelasController::class, 'index']);

            Route::get('aktivitas-mahasiswa', [AktivitasMahasiswaController::class, 'index']);
            Route::post('aktivitas-mahasiswa', [AktivitasMahasiswaController::class, 'store']);

            Route::get('bimbingan-mahasiswa', [BimbinganMahasiswa::class, 'index']);
            Route::post('bimbingan-mahasiswa', [BimbinganMahasiswa::class, 'store']);

            Route::get('ref-agama', [ReferensiController::class, 'agama']);
            Route::get('ref-alat-transportasi', [ReferensiController::class, 'alatTransportasi']);
            Route::get('ref-jalur-daftar', [ReferensiController::class, 'jalurDaftar']);
            Route::get('ref-jenis-aktivitas', [ReferensiController::class, 'jenisAktivitas']);
            Route::get('ref-jenis-tinggal', [ReferensiController::class, 'jenisTinggal']);
            Route::get('ref-jenjang-pendidikan', [ReferensiController::class, 'jenjangPendidikan']);
            Route::get('ref-kebutuhan-khusus', [ReferensiController::class, 'kebutuhanKhusus']);
            Route::get('ref-negara', [ReferensiController::class, 'negara']);
            Route::get('ref-pekerjaan', [ReferensiController::class, 'pekerjaan']);
            Route::get('ref-pembiayaan', [ReferensiController::class, 'pembiayaan']);
            Route::get('ref-penghasilan', [ReferensiController::class, 'penghasilan']);
            Route::get('ref-wilayah', [ReferensiController::class, 'wilayah']);
            Route::get('ref-prodi', [ReferensiController::class, 'prodi']);
            Route::get('ref-wilayah-provinsi', [ReferensiController::class, 'wilayahProvinsi']);
            Route::get('ref-wilayah-kota', [ReferensiController::class, 'wilayahKota']);
            Route::get('ref-wilayah-kecamatan', [ReferensiController::class, 'wilayahKecamatan']);
            Route::get('ref-dosen', [ReferensiController::class, 'dosen']);
            Route::get('log-import', [LogImportController::class, 'index']);

            Route::get('informasi', [InformasiController::class, 'index']);
        });

        Route::get('error', function () {
            return view('layouts.error');
        });

        Route::get('logout', [ProfilController::class, 'logout']);

        Route::get('sandbox', [SandboxController::class, 'index']);
        Route::put('sandbox', [SandboxController::class, 'update']);
    });
});



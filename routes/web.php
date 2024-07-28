<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\KomponenPenilaianController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\SoalController;

// Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('login', [AuthController::class, 'login']);
// Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/makesession-pegawai', [LoginController::class, 'make_session_pegawa'])->name('makesession_pegawai');
Route::get('/makesession-mahasiswa', [LoginController::class, 'make_session_mahasiswa'])->name('make_session_mahasiswa');
Route::get('/makesession-dosen', [LoginController::class, 'make_session_dosen'])->name('make_session_dosen');



Route::middleware(['cekmahasiswa'])->group(function () {
Route::get('dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
Route::get('home', [MahasiswaController::class, 'index'])->name('home');
Route::get('soal', [MahasiswaController::class, 'show'])->name('soal.page');
Route::get('/get-komponen-penilaian', [MahasiswaController::class, 'getKomponenPenilaian']);
Route::get('/get-soal', [MahasiswaController::class, 'getSoal']);
Route::post('/submit-jawaban', [MahasiswaController::class, 'store']);
Route::get('/admin/change_session', [AdminController::class, 'change_session'])->name('change_session');
Route::get('/admin/getsession_ta', [AdminController::class, 'getsession_ta'])->name('getsession_ta');
Route::get('/check-kuisioner-status', [MahasiswaController::class, 'checkKuisionerStatus'])->name('check.kuisioner.status');
});

Route::middleware(['cekpegawai'])->group(function () {
Route::get('/admin/komponen-penilaian', [KomponenPenilaianController::class, 'index'])->name('komponen-penilaian.index');
Route::get('/admin/komponen-penilaian/data', [KomponenPenilaianController::class, 'getData'])->name('komponen-penilaian.data');
Route::post('/admin/komponen-penilaian', [KomponenPenilaianController::class, 'store'])->name('komponen-penilaian.store');
Route::put('/admin/komponen-penilaian/{id}', [KomponenPenilaianController::class, 'update']);
Route::delete('/admin/komponen-penilaian/{id}', [KomponenPenilaianController::class, 'destroy']);
Route::get('/admin/komponen-penilaian/{id}', [KomponenPenilaianController::class, 'show']);


Route::get('/admin/soal', [SoalController::class, 'index'])->name('soal.index');
Route::get('/admin/soal/data', [SoalController::class, 'getData'])->name('soal.data');
Route::post('/admin/soal', [SoalController::class, 'store'])->name('soal.store');
Route::put('/admin/soal/{id}', [SoalController::class, 'update']);
Route::delete('/admin/soal/{id}', [SoalController::class, 'destroy']);
Route::get('/admin/soal/{id}', [SoalController::class, 'show']);
Route::get('/admin/soal/komponen-options', [KomponenPenilaianController::class, 'getData'])->name('soal.komponen-options');
Route::get('/admin/mreg/data', [SoalController::class, 'getDataMreg'])->name('mreg.data');

Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});
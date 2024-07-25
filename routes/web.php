<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MahasiswaController;

// Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('login', [AuthController::class, 'login']);
// Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/makesession-pegawai', [LoginController::class, 'make_session_pegawa'])->name('makesession_pegawai');
Route::get('/makesession-mahasiswa', [LoginController::class, 'make_session_mahasiswa'])->name('make_session_mahasiswa');
Route::get('/makesession-dosen', [LoginController::class, 'make_session_dosen'])->name('make_session_dosen');

Route::middleware(['ceklogin'])->group(function () {
Route::get('home', [MahasiswaController::class, 'index'])->name('home');
Route::get('soal', [MahasiswaController::class, 'show'])->name('soal.page');
Route::get('/get-komponen-penilaian', [MahasiswaController::class, 'getKomponenPenilaian']);
Route::get('/get-soal', [MahasiswaController::class, 'getSoal']);
Route::post('/submit-jawaban', [MahasiswaController::class, 'store']);

});
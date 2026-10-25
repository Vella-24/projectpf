<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ValidatorController;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke daftar pertanyaan Data Entry
Route::get('/', function () {
    return redirect()->route('pertanyaan.index');
});

Route::get('/staff', [PertanyaanController::class, 'indexStaff'])->name('staff.index');

Route::get('/login', [LoginController::class, 'login']);

Route::post('/login', [LoginController::class, 'prosesLogin']);

Route::get('/logout', [LoginController::class, 'logout']);

// Resource Route untuk CRUD Pertanyaan (FAQ)
Route::resource('pertanyaan', PertanyaanController::class);

// Resource Route untuk CRUD Kategori
Route::resource('kategori', KategoriController::class);

// Route validator akademik menangani dashboard review dan validasi FAQ
Route::prefix('validator')->group(function () {
    Route::get('/', [ValidatorController::class, 'index'])->name('validator.index');
    Route::get('/pertanyaan/{id}', [ValidatorController::class, 'show'])->name('validator.show');
    Route::put('/pertanyaan/{id}/status', [ValidatorController::class, 'updateStatus'])->name('validator.updateStatus');
});
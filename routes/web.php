<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ValidatorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Redirect halaman utama ke daftar pertanyaan Data Entry
// Route::get('/', function () {
//     return redirect()->route('mahasiswa.index');
// });

Route::get('/', [PertanyaanController::class, 'indexMahasiswa'])->name('mahasiswa.index');

Route::get('/staff', [PertanyaanController::class, 'index'])->name('pertanyaan.index');

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

// Super Admin Routes
Route::prefix('superadmin')->group(function () {
    // Kelola Pertanyaan (Superadmin CRUD - pertanyaan disetujui / dipublikasikan)
    Route::get('/pertanyaan', [AdminController::class, 'pertanyaanIndex'])->name('superadmin.pertanyaan.index');
    Route::get('/pertanyaan/create', [AdminController::class, 'pertanyaanCreate'])->name('superadmin.pertanyaan.create');
    Route::post('/pertanyaan', [AdminController::class, 'pertanyaanStore'])->name('superadmin.pertanyaan.store');
    Route::get('/pertanyaan/{id}/edit', [AdminController::class, 'pertanyaanEdit'])->name('superadmin.pertanyaan.edit');
    Route::put('/pertanyaan/{id}', [AdminController::class, 'pertanyaanUpdate'])->name('superadmin.pertanyaan.update');
    Route::delete('/pertanyaan/{id}', [AdminController::class, 'pertanyaanDestroy'])->name('superadmin.pertanyaan.destroy');

    // Manajemen Pengguna
    Route::get('/', [AdminController::class, 'index'])->name('superadmin.index');
    Route::get('/create', [AdminController::class, 'create'])->name('superadmin.users.create');
    Route::post('/', [AdminController::class, 'store'])->name('superadmin.users.store');
    Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('superadmin.users.edit');
    Route::put('/{id}', [AdminController::class, 'update'])->name('superadmin.users.update');
    Route::delete('/{id}', [AdminController::class, 'destroy'])->name('superadmin.users.destroy');
});

Route::get('/mahasiswa/search', [PertanyaanController::class, 'search'])
    ->name('mahasiswa.search');
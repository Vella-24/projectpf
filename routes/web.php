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

Route::get('/superadmin', [AdminController::class, 'index'])
    ->name('superadmin.index');

Route::get('/superadmin/create', [AdminController::class, 'create'])
    ->name('superadmin.users.create');

Route::post('/superadmin', [AdminController::class, 'store'])
    ->name('superadmin.users.store');

Route::get('/superadmin/{id}/edit', [AdminController::class, 'edit'])
    ->name('superadmin.users.edit');

Route::put('/superadmin/{id}', [AdminController::class, 'update'])
    ->name('superadmin.users.update');

Route::delete('/superadmin/{id}', [AdminController::class, 'destroy'])
    ->name('superadmin.users.destroy');
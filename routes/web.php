<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ValidatorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PertanyaanLanjutanController;
use App\Http\Middleware\CheckRole;

// Redirect halaman utama ke daftar pertanyaan Data Entry
Route::get('/', [PertanyaanController::class, 'indexMahasiswa'])->name('mahasiswa.index');

// Route Data Entry
Route::prefix('staff')->middleware(CheckRole::class . ':data_entry')->group(function () {

    // Dashboard Data Entry
    Route::get('/', [PertanyaanController::class, 'index'])->name('pertanyaan.index');

    // CRUD Pertanyaan
    Route::resource('pertanyaan', PertanyaanController::class)->except(['index']);

    // Daftar pertanyaan lanjutan
    Route::get('/pertanyaan-lanjutan', [PertanyaanLanjutanController::class, 'index'])->name('pertanyaanLanjutan.index');

    // Mengambil pertanyaan lanjutan
    Route::post('/pertanyaan-lanjutan/{id}/ambil', [PertanyaanLanjutanController::class, 'ambil'])->name('pertanyaanLanjutan.ambil');

    // Halaman menjawab pertanyaan lanjutan
    Route::get('/pertanyaan-lanjutan/{id}/jawab', [PertanyaanLanjutanController::class, 'jawab'])->name('pertanyaanLanjutan.jawab');

    // Menyimpan jawaban
    Route::post('/pertanyaan-lanjutan/{id}/jawab', [PertanyaanLanjutanController::class, 'simpanJawaban'])->name('pertanyaanLanjutan.simpanJawaban');
});

Route::get('/login', [LoginController::class, 'login']);

Route::post('/login', [LoginController::class, 'prosesLogin']);

Route::get('/logout', [LoginController::class, 'logout']);

// Resource Route untuk CRUD Pertanyaan (FAQ)
Route::resource('pertanyaan', PertanyaanController::class)
    ->except(['index']);

// Resource Route untuk CRUD Kategori
Route::resource('kategori', KategoriController::class);

// Route validator akademik menangani dashboard review dan validasi FAQ
Route::prefix('validator')->middleware(CheckRole::class . ':validator')->group(function () {
    Route::get('/', [ValidatorController::class, 'index'])->name('validator.index');
    Route::get('/pertanyaan/{id}', [ValidatorController::class, 'show'])->name('validator.show');
    Route::put('/pertanyaan/{id}/status', [ValidatorController::class, 'updateStatus'])->name('validator.updateStatus');
    Route::post(
    '/{id}/ambil-review',
    [ValidatorController::class, 'ambilReview']
)->name('validator.ambilReview');
    });

// Super Admin Routes
Route::prefix('superadmin')->middleware(CheckRole::class . ':super_admin')->group(function () {
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

Route::get('/mahasiswa/search', [PertanyaanController::class, 'indexMahasiswa'])
    ->name('mahasiswa.search');

// Mahasiswa mengajukan pertanyaan lanjutan
Route::get(
    '/mahasiswa/pertanyaan-lanjutan/{id}',
    [PertanyaanLanjutanController::class, 'create']
)->name('mahasiswa.pertanyaanLanjutan.create');

Route::post(
    '/mahasiswa/pertanyaan-lanjutan',
    [PertanyaanLanjutanController::class, 'store']
)->name('mahasiswa.pertanyaanLanjutan.store');

//route data entry
// Route::get(
//     '/pertanyaan-lanjutan',
//     [PertanyaanLanjutanController::class, 'index']
// )->name('pertanyaanLanjutan.index');

// Route::post(
//     '/pertanyaan-lanjutan/{id}/ambil',
//     [PertanyaanLanjutanController::class, 'ambil']
// )->name('pertanyaanLanjutan.ambil');

// // Halaman Data Entry menuliskan jawaban
// Route::get(
//     '/pertanyaan-lanjutan/{id}/jawab',
//     [PertanyaanLanjutanController::class, 'jawab']
// )->name('pertanyaanLanjutan.jawab');

// // Menyimpan draft atau mengirim jawaban ke Validator
// Route::post(
//     '/pertanyaan-lanjutan/{id}/jawab',
//     [PertanyaanLanjutanController::class, 'simpanJawaban']
// )->name('pertanyaanLanjutan.simpanJawaban');

// Route::get('/staff', [PertanyaanController::class, 'index'])->name('pertanyaan.index');
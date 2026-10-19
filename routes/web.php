<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PertanyaanController;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke daftar pertanyaan Data Entry
Route::get('/', function () {
    return redirect()->route('pertanyaan.index');
});

// Resource Route untuk CRUD Pertanyaan (FAQ)
Route::resource('pertanyaan', PertanyaanController::class);

// Resource Route untuk CRUD Kategori
Route::resource('kategori', KategoriController::class);

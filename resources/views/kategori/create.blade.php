@extends('layouts.superadmin')

@section('title', 'Tambah Kategori - ASK UBAYA')
@section('page_title', 'Tambah Kategori')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('kategori.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Kelola Kategori</span>
        </a>
    </div>

    <!-- Header Form -->
    <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Tambah Kategori Baru</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Kategori yang dibuat di sini akan otomatis muncul dan dapat dipilih oleh Staff Data Entry.
        </p>
    </div>

    <!-- Alert Error Validasi jika ada -->
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs shadow-2xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                </svg>
                <span>Mohon periksa kesalahan berikut:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 pl-6 text-rose-700">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
        <form action="{{ route('kategori.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama Kategori -->
            <div>
                <label for="nama_kategori" class="block text-xs font-bold text-gray-800 mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="nama_kategori" 
                       name="nama_kategori" 
                       value="{{ old('nama_kategori') }}" 
                       required 
                       placeholder="Contoh: Beasiswa & Keuangan, Kemahasiswaan, Magang" 
                       class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-2xs">
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-gray-800 mb-1.5">
                    Deskripsi Kategori <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <textarea id="deskripsi" 
                          name="deskripsi" 
                          rows="4" 
                          placeholder="Jelaskan cakupan topik atau informasi yang termasuk dalam kategori ini..." 
                          class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-2xs resize-y">{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('kategori.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#741228] hover:bg-[#5e0d1f] text-white text-xs sm:text-sm font-bold transition shadow-sm hover:shadow">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

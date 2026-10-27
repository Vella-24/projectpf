@extends('layouts.data-entry')

@section('title', 'ASK UBAYA - Detail Pertanyaan')
@section('page_title', 'Detail Pertanyaan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Sub-Bar Navigasi & Aksi -->
    <div class="flex items-center justify-between pb-2">
        <a href="{{ route('pertanyaan.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>

        <div class="flex items-center gap-3">
            <!-- Edit Button -->
            <a href="{{ route('pertanyaan.edit', $pertanyaan->id) }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Pertanyaan</span>
            </a>

            <!-- Hapus Button -->
            <form action="{{ route('pertanyaan.destroy', $pertanyaan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-2xl text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Judul Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
            Detail Pengetahuan
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
            Informasi lengkap pertanyaan, jawaban terverifikasi, dan status validasi.
        </p>
    </div>

    <!-- Catatan Validator jika ada -->
    @if($pertanyaan->catatan_validator)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-2xl text-xs shadow-sm flex items-start gap-3">
            <div class="w-6 h-6 rounded-full bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-amber-900">Catatan dari Validator:</p>
                <p class="mt-0.5 text-amber-800 font-medium leading-relaxed">{{ $pertanyaan->catatan_validator }}</p>
            </div>
        </div>
    @endif

    <!-- Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Main Content Card (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
            
            <!-- Pertanyaan -->
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">PERTANYAAN</span>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 leading-snug">
                    {{ $pertanyaan->judul_pertanyaan }}
                </h2>
            </div>

            <hr class="border-gray-100">

            <!-- Jawaban -->
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">JAWABAN TERVERIFIKASI</span>
                <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-100 text-gray-800 text-xs sm:text-sm font-medium leading-relaxed whitespace-pre-line">
                    {{ $pertanyaan->isi_jawaban }}
                </div>
            </div>

            <!-- Kata Kunci / Tags -->
            @if($pertanyaan->tags && $pertanyaan->tags->count() > 0)
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">KATA KUNCI / TAG</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pertanyaan->tags as $t)
                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-semibold bg-rose-50 text-[#741228] border border-rose-200/80">
                                {{ $t->nama_tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Meta Sidebar Card (1 Col) -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-5">
            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">
                INFORMASI DOKUMEN
            </h3>

            <!-- Status -->
            <div class="pb-3 border-b border-gray-50">
                <span class="text-[11px] text-gray-400 font-medium block mb-1">Status Validasi</span>
                @if($pertanyaan->status === 'menunggu_validasi')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Menunggu Validasi
                    </span>
                @elseif($pertanyaan->status === 'disetujui')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Telah Diterbitkan
                    </span>
                @elseif($pertanyaan->status === 'ditolak')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Perlu Direvisi
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        Draf
                    </span>
                @endif
            </div>

            <!-- Kategori -->
            <div class="pb-3 border-b border-gray-50">
                <span class="text-[11px] text-gray-400 font-medium block mb-0.5">Kategori</span>
                <p class="text-xs font-bold text-gray-800">
                    {{ $pertanyaan->kategori->nama_kategori ?? '-' }}
                </p>
            </div>

            <!-- Petugas Input -->
            <div class="pb-3 border-b border-gray-50">
                <span class="text-[11px] text-gray-400 font-medium block mb-0.5">Petugas Input</span>
                <p class="text-xs font-bold text-gray-800">
                    {{ $pertanyaan->user->name ?? 'Data Entry UBAYA' }}
                </p>
            </div>

            <!-- Waktu Dibuat -->
            <div class="pb-3 border-b border-gray-50">
                <span class="text-[11px] text-gray-400 font-medium block mb-0.5">Waktu Dibuat</span>
                <p class="text-xs font-semibold text-gray-600">
                    {{ $pertanyaan->created_at ? $pertanyaan->created_at->isoFormat('D MMMM Y, HH:mm') : '-' }}
                </p>
            </div>

            <!-- Waktu Diperbarui -->
            <div>
                <span class="text-[11px] text-gray-400 font-medium block mb-0.5">Waktu Diperbarui</span>
                <p class="text-xs font-semibold text-gray-600">
                    {{ $pertanyaan->updated_at ? $pertanyaan->updated_at->isoFormat('D MMMM Y, HH:mm') : '-' }}
                </p>
            </div>

        </div>

    </div>

</div>
@endsection

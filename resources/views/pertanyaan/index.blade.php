@extends('layouts.data-entry')

@section('title', 'ASK UBAYA - Beranda Data Entry')
@section('page_title', 'Beranda')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Greeting & Quick Action Banner (Gambar 2) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">
                Selamat datang, {{ session('user.name') ?? 'Staff Data Entry' }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
                Berikut ringkasan kontribusi pengetahuanmu hari ini. Klik kartu di bawah untuk memfilter daftar pertanyaan secara instan.
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('pertanyaan.create') }}" 
               class="inline-flex items-center gap-2 bg-[#741228] hover:bg-[#5e0d1f] text-white px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all duration-200 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Pengetahuan</span>
            </a>
        </div>
    </div>

    <!-- 4 Kartu Ringkasan Interaktif / Quick Filter (Klik untuk filter instan) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
        
        <!-- 1. Total Kontribusi (Semua Status) -->
        @php
            $isSemuaAktif = !request()->filled('status');
        @endphp
        <a href="{{ route('pertanyaan.index', array_merge(request()->except('status', 'page'), [])) }}" 
           data-filter-card
           data-status=""
           data-color="maroon"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isSemuaAktif ? 'border-[#741228] ring-2 ring-[#741228]/15 bg-rose-50/10 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#741228] flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#741228]/10 text-[#741228] {{ $isSemuaAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900 leading-none mb-1 group-hover:text-[#741228] transition">
                    {{ $totalSemua ?? $pertanyaans->total() }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Total kontribusi</p>
            </div>
        </a>

        <!-- 2. Menunggu Validasi -->
        @php
            $isMenungguAktif = request('status') === 'menunggu_validasi';
        @endphp
        <a href="{{ route('pertanyaan.index', array_merge(request()->except('status', 'page'), ['status' => 'menunggu_validasi'])) }}" 
           data-filter-card
           data-status="menunggu_validasi"
           data-color="amber"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isMenungguAktif ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/20 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 {{ $isMenungguAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900 leading-none mb-1 group-hover:text-amber-600 transition">
                    {{ $totalMenunggu ?? 0 }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Menunggu validasi</p>
            </div>
        </a>

        <!-- 3. Telah Diterbitkan (Disetujui) -->
        @php
            $isDisetujuiAktif = request('status') === 'disetujui';
        @endphp
        <a href="{{ route('pertanyaan.index', array_merge(request()->except('status', 'page'), ['status' => 'disetujui'])) }}" 
           data-filter-card
           data-status="disetujui"
           data-color="emerald"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isDisetujuiAktif ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 {{ $isDisetujuiAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900 leading-none mb-1 group-hover:text-emerald-600 transition">
                    {{ $totalDisetujui ?? 0 }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Telah diterbitkan</p>
            </div>
        </a>

        <!-- 4. Perlu Direvisi (Ditolak) -->
        @php
            $isDitolakAktif = request('status') === 'ditolak';
        @endphp
        <a href="{{ route('pertanyaan.index', array_merge(request()->except('status', 'page'), ['status' => 'ditolak'])) }}" 
           data-filter-card
           data-status="ditolak"
           data-color="rose"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isDitolakAktif ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/20 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 {{ $isDitolakAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900 leading-none mb-1 group-hover:text-rose-600 transition">
                    {{ $totalDitolak ?? 0 }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Perlu direvisi</p>
            </div>
        </a>

    </div>

    <!-- Tabel Kontribusi Terbaru (Dengan id area-konten-tabel untuk update instan tanpa reload lambat) -->
    <div id="area-konten-tabel" class="relative overflow-hidden bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm flex flex-col space-y-5 transition-all duration-150">
        <!-- Indikator Bar Tipis di bagian atas (Hanya muncul jika belum ada di memori cache) -->
        <div id="table-loader-bar" class="hidden absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#741228] via-[#e11d48] to-[#741228] z-20 animate-pulse"></div>
        
        <!-- Header Kontribusi Terbaru -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-1">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <span>Kontribusi terbaru</span>
                    @if(request()->filled('status'))
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600">
                            Status: {{ ucwords(str_replace('_', ' ', request('status'))) }}
                        </span>
                    @endif
                </h2>
                <p class="text-xs text-gray-400 font-medium">Pantau status, lakukan peninjauan, atau perbarui konten pertanyaan.</p>
            </div>
            @if(request()->hasAny(['q', 'kategori_id', 'status']))
                <a href="{{ route('pertanyaan.index') }}" class="btn-ajax-reset text-xs font-bold text-[#741228] hover:underline flex items-center gap-1 transition">
                    Tampilkan Semua Pertanyaan &rarr;
                </a>
            @endif
        </div>

        <!-- Filter & Pencarian Bar -->
        <form id="form-filter" action="{{ route('pertanyaan.index') }}" method="GET" class="bg-gray-50/70 p-3.5 rounded-2xl border border-gray-100 flex flex-wrap items-center gap-3">
            
            <!-- Input Cari Kata Kunci -->
            <div class="relative flex-1 min-w-[220px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" 
                       placeholder="Cari pertanyaan / jawaban.." 
                       class="w-full pl-9 pr-3 py-2 bg-white rounded-xl border border-gray-200 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition">
            </div>

            <!-- Dropdown Kategori -->
            <div class="min-w-[160px]">
                <select name="kategori_id" 
                        class="w-full px-3 py-2 bg-white rounded-xl border border-gray-200 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Dropdown Status -->
            <div class="min-w-[160px]">
                <select name="status" 
                        class="w-full px-3 py-2 bg-white rounded-xl border border-gray-200 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draf</option>
                    <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Diterbitkan</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Revisi / Ditolak</option>
                </select>
            </div>

            <!-- Action Button Filter & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="px-5 py-2 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-xl text-xs font-bold transition shadow-sm">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'kategori_id', 'status']))
                    <a href="{{ route('pertanyaan.index') }}" 
                       class="btn-ajax-reset px-3.5 py-2 bg-white hover:bg-gray-100 border border-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Tabel Kontribusi -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Pertanyaan</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Diperbarui</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs">
                    @forelse($pertanyaans as $item)
                        <tr class="hover:bg-rose-50/20 transition group">
                            <!-- Judul Pertanyaan -->
                            <td class="py-4 px-4 max-w-md">
                                <a href="{{ route('pertanyaan.show', $item->id) }}" class="font-bold text-gray-800 hover:text-[#741228] block truncate text-sm">
                                    {{ $item->judul_pertanyaan }}
                                </a>
                                <p class="text-xs text-gray-400 truncate mt-0.5">
                                    {{ Str::limit($item->isi_jawaban, 80) }}
                                </p>
                            </td>

                            <!-- Kategori -->
                            <td class="py-4 px-4 whitespace-nowrap text-gray-600 font-medium">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium">
                                    {{ $item->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>

                            <!-- Status Badge dengan Titik Warna (Dot) -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($item->status === 'menunggu_validasi')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu
                                    </span>
                                @elseif($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Diterbitkan
                                    </span>
                                @elseif($item->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Draf
                                    </span>
                                @endif
                            </td>

                            <!-- Diperbarui -->
                            <td class="py-4 px-4 whitespace-nowrap text-gray-400 text-xs font-medium">
                                @if($item->updated_at)
                                    @if($item->updated_at->isToday())
                                        Hari ini, {{ $item->updated_at->format('H:i') }}
                                    @elseif($item->updated_at->isYesterday())
                                        Kemarin, {{ $item->updated_at->format('H:i') }}
                                    @else
                                        {{ $item->updated_at->format('j M Y') }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Detail Button -->
                                    <a href="{{ route('pertanyaan.show', $item->id) }}" 
                                       title="Lihat Detail" 
                                       class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('pertanyaan.edit', $item->id) }}" 
                                       title="Edit Pertanyaan" 
                                       class="p-2 text-gray-400 hover:text-[#741228] hover:bg-rose-50 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <!-- Hapus Button (Form method delete) -->
                                    <form action="{{ route('pertanyaan.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Pertanyaan" 
                                                class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>

                                    <!-- Chevron / Arrow Link -->
                                    <a href="{{ route('pertanyaan.show', $item->id) }}" class="text-gray-300 hover:text-[#741228] p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium text-sm text-gray-500">Tidak ada pertanyaan ditemukan dengan filter ini.</p>
                                    @if(request()->hasAny(['q', 'kategori_id', 'status']))
                                        <a href="{{ route('pertanyaan.index') }}" class="btn-ajax-reset text-xs text-[#741228] font-bold mt-2 hover:underline">
                                            Bersihkan filter pencarian
                                        </a>
                                    @else
                                        <a href="{{ route('pertanyaan.create') }}" class="text-xs text-[#741228] font-bold mt-2 hover:underline">
                                            + Tambah pertanyaan pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Tombol Tambah Cepat & Pagination -->
        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('pertanyaan.create') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-[#741228] hover:text-[#5e0d1f] hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah pengetahuan baru</span>
            </a>

            <!-- Pagination (Support AJAX Pagination) -->
            <div id="area-pagination" class="flex items-center gap-2 text-xs">
                @if ($pertanyaans->onFirstPage())
                    <span class="px-3 py-1.5 rounded-xl border border-gray-200 text-gray-300 cursor-not-allowed select-none">
                        &laquo; Previous
                    </span>
                @else
                    <a href="{{ $pertanyaans->previousPageUrl() }}" 
                       class="btn-ajax-page px-3 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 font-medium transition shadow-sm">
                        &laquo; Previous
                    </a>
                @endif

                @if ($pertanyaans->hasMorePages())
                    <a href="{{ $pertanyaans->nextPageUrl() }}" 
                       class="btn-ajax-page px-3 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 font-medium transition shadow-sm">
                        Next &raquo;
                    </a>
                @else
                    <span class="px-3 py-1.5 rounded-xl border border-gray-200 text-gray-300 cursor-not-allowed select-none">
                        Next &raquo;
                    </span>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    // Fitur Instant Cache & Background Prefetching (0ms response, tanpa efek loading lambat)
    document.addEventListener('DOMContentLoaded', function () {
        const areaTabel = document.getElementById('area-konten-tabel');
        if (!areaTabel) return;

        // Cache memori browser untuk menyimpan DOM tabel tiap status
        const pageCache = new Map();
        const prefetchSet = new Set();

        // Fungsi normalisasi key URL agar konsisten
        function getCacheKey(url) {
            try {
                const u = new URL(url, window.location.origin);
                return u.pathname + u.search;
            } catch (e) {
                return url;
            }
        }

        // Simpan tampilan saat ini ke dalam cache awal
        const currentKey = getCacheKey(window.location.href);
        pageCache.set(currentKey, areaTabel.innerHTML);

        function showLoader(show) {
            const loader = document.getElementById('table-loader-bar');
            if (loader) {
                if (show) {
                    loader.classList.remove('hidden');
                } else {
                    loader.classList.add('hidden');
                }
            }
        }

        // Prefetch data halaman di background (diam-diam)
        function prefetchUrl(url) {
            if (!url) return;
            const key = getCacheKey(url);
            if (pageCache.has(key) || prefetchSet.has(key)) return;

            prefetchSet.add(key);
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('area-konten-tabel');
                if (newContent) {
                    pageCache.set(key, newContent.innerHTML);
                }
            })
            .catch(() => {})
            .finally(() => {
                prefetchSet.delete(key);
            });
        }

        // Prefetch semua 4 kartu filter secara otomatis begitu halaman dimuat
        document.querySelectorAll('[data-filter-card]').forEach(card => {
            const url = card.getAttribute('href');
            if (url) {
                prefetchUrl(url);
                // Prefetch instan saat kursor mouse mulai menyentuh kartu (hover)
                card.addEventListener('mouseenter', function () {
                    prefetchUrl(url);
                });
            }
        });

        // Fungsi perpindahan halaman super cepat
        function loadHalaman(url, statusBaru) {
            const key = getCacheKey(url);

            // 1. Update visual kartu aktif seketika (0 ms)
            updateKartuAktif(statusBaru);

            // 2. JIKA SUDAH ADA DI CACHE: Ganti isi tabel INSTAN tanpa loading sama sekali (0 ms)
            if (pageCache.has(key)) {
                areaTabel.innerHTML = pageCache.get(key);
                window.history.pushState({ status: statusBaru }, '', url);
                return;
            }

            // 3. JIKA BELUM ADA DI CACHE (misal pencarian kata kunci baru):
            // Tampilkan bar progress merah halus di bagian atas tanpa meredupkan/mengaburkan tabel
            showLoader(true);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('area-konten-tabel');
                if (newContent) {
                    pageCache.set(key, newContent.innerHTML);
                    areaTabel.innerHTML = newContent.innerHTML;
                }
                window.history.pushState({ status: statusBaru }, '', url);
            })
            .catch(() => {
                window.location.href = url;
            })
            .finally(() => {
                showLoader(false);
            });
        }

        // Update highlight border & badge aktif kartu
        function updateKartuAktif(statusBaru) {
            const cards = document.querySelectorAll('[data-filter-card]');
            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const badge = card.querySelector('.badge-aktif');
                const color = card.getAttribute('data-color');

                if (cardStatus === statusBaru) {
                    card.classList.add('ring-2', 'shadow-sm');
                    if (color === 'maroon') {
                        card.classList.add('border-[#741228]', 'ring-[#741228]/15', 'bg-rose-50/10');
                    } else if (color === 'amber') {
                        card.classList.add('border-amber-500', 'ring-amber-500/20', 'bg-amber-50/20');
                    } else if (color === 'emerald') {
                        card.classList.add('border-emerald-500', 'ring-emerald-500/20', 'bg-emerald-50/20');
                    } else if (color === 'rose') {
                        card.classList.add('border-rose-500', 'ring-rose-500/20', 'bg-rose-50/20');
                    }
                    card.classList.remove('border-gray-100');
                    if (badge) badge.classList.remove('hidden');
                } else {
                    card.classList.remove('ring-2', 'shadow-sm', 'border-[#741228]', 'ring-[#741228]/15', 'bg-rose-50/10', 'border-amber-500', 'ring-amber-500/20', 'bg-amber-50/20', 'border-emerald-500', 'ring-emerald-500/20', 'bg-emerald-50/20', 'border-rose-500', 'ring-rose-500/20', 'bg-rose-50/20');
                    card.classList.add('border-gray-100');
                    if (badge) badge.classList.add('hidden');
                }
            });

            // Sinkronkan pilihan dropdown status di dalam form filter
            const selectStatus = document.querySelector('select[name="status"]');
            if (selectStatus) {
                selectStatus.value = statusBaru;
            }
        }

        // Event listener saat mengklik salah satu dari 4 kartu
        document.querySelectorAll('[data-filter-card]').forEach(card => {
            card.addEventListener('click', function (e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                const status = this.getAttribute('data-status') || '';
                loadHalaman(url, status);
            });
        });

        // Event listener delegasi pada areaTabel (Reset filter & Pagination)
        areaTabel.addEventListener('click', function (e) {
            const linkReset = e.target.closest('.btn-ajax-reset');
            const linkPage = e.target.closest('.btn-ajax-page');

            if (linkReset) {
                e.preventDefault();
                loadHalaman(linkReset.getAttribute('href'), '');
            } else if (linkPage) {
                e.preventDefault();
                const currentStatus = new URLSearchParams(window.location.search).get('status') || '';
                loadHalaman(linkPage.getAttribute('href'), currentStatus);
            }
        });

        // Event listener pencarian & filter form tanpa reload
        areaTabel.addEventListener('submit', function (e) {
            const form = e.target.closest('#form-filter');
            if (form) {
                e.preventDefault();
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const url = form.action + '?' + params.toString();
                const status = formData.get('status') || '';
                loadHalaman(url, status);
            }
        });

        // Navigasi Back / Forward history browser
        window.addEventListener('popstate', function (e) {
            const targetUrl = window.location.href;
            const targetKey = getCacheKey(targetUrl);
            const status = (e.state && e.state.status) !== undefined ? e.state.status : (new URLSearchParams(window.location.search).get('status') || '');
            
            if (pageCache.has(targetKey)) {
                areaTabel.innerHTML = pageCache.get(targetKey);
                updateKartuAktif(status);
            } else {
                window.location.reload();
            }
        });
    });
</script>
@endsection

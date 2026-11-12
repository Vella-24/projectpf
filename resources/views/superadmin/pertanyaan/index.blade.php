@extends('layouts.superadmin')

@section('title', 'Kelola Pengetahuan - Super Admin ASK UBAYA')
@section('page_title', 'Kelola Pengetahuan')

@section('content')
<div class="space-y-6">

    <!-- Header & Tombol Tambah Pengetahuan -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Kelola Pengetahuan</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Daftar pengetahuan yang sudah disetujui dan draft milik Anda. Hanya yang berstatus "Disetujui" yang tampil di halaman mahasiswa.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.pertanyaan.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-[#741228] hover:bg-[#5e0d1f] text-white transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span> Buat Pengetahuan</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-xs sm:text-sm flex items-center gap-2.5 shadow-2xs mb-6">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">{{ session('sukses') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl text-xs sm:text-sm flex items-center gap-2.5 shadow-2xs mb-6">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
            </svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter & Pencarian Bar -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-sm">
        <form action="{{ route('superadmin.pertanyaan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <!-- Input Cari Kata Kunci -->
            <div class="sm:col-span-5 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari pertanyaan, jawaban, atau tag..." 
                       class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition">
            </div>

            <!-- Dropdown Filter Kategori -->
            <div class="sm:col-span-3">
                <select name="kategori_id" 
                        class="w-full py-2.5 px-3 bg-gray-50/70 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 font-medium focus:bg-white focus:outline-none focus:border-[#741228] transition cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Dropdown Filter Status -->
            <div class="sm:col-span-2">
                <select name="status" 
                        class="w-full py-2.5 px-3 bg-gray-50/70 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 font-medium focus:bg-white focus:outline-none focus:border-[#741228] transition cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="w-full py-2.5 px-3 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-xl text-xs font-bold transition text-center shadow-2xs">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'kategori_id', 'status']))
                <a href="{{ route('superadmin.pertanyaan.index') }}" 
                   class="py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition text-center" title="Reset Filter">
                    ✕
                </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Tabel Daftar Pengetahuan -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70 text-[11px] font-extrabold uppercase tracking-wider text-gray-400">
                        <th class="py-4 px-6 w-14 text-center">NO</th>
                        <th class="py-4 px-6 min-w-[280px]">PERTANYAAN & JAWABAN</th>
                        <th class="py-4 px-6">KATEGORI</th>
                        <th class="py-4 px-6">PENULIS</th>
                        <th class="py-4 px-6 text-center">STATUS</th>
                        <th class="py-4 px-6 text-center w-36">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-xs sm:text-sm">
                    @forelse($pertanyaans as $index => $item)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-4 px-6 text-center text-gray-400 font-semibold">
                            {{ $pertanyaans->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-6">
                            <h4 class="font-bold text-gray-900 leading-snug line-clamp-1 mb-1">
                                {{ $item->judul_pertanyaan }}
                            </h4>
                            <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">
                                {{ Str::limit(strip_tags($item->isi_jawaban), 120) }}
                            </p>
                            @if($item->tags && $item->tags->count() > 0)
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($item->tags->take(3) as $t)
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-semibold">#{{ $t->nama_tag }}</span>
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#FFF5F6] text-[#741228] border border-rose-100">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <p class="font-bold text-gray-800 text-xs">{{ $item->user->name ?? 'Sistem' }}</p>
                            <p class="text-[11px] text-gray-400 capitalize">{{ str_replace('_', ' ', $item->user->role ?? 'User') }}</p>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($item->status === 'draft')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Draf
                                </span>
                            @elseif($item->status === 'menunggu_validasi')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Menunggu Validasi
                                </span>
                            @elseif($item->status === 'ditolak')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Disetujui
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('superadmin.pertanyaan.edit', $item->id) }}" 
                                   class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition" 
                                   title="Ubah Pertanyaan">
                                    Edit
                                </a>
                                <form action="{{ route('superadmin.pertanyaan.destroy', $item->id) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-100 text-xs font-bold text-rose-700 hover:bg-rose-100 transition" 
                                            title="Hapus Pertanyaan">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 px-6 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-bold text-gray-700 text-sm">Tidak Ada Pertanyaan Ditemukan</p>
                            <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci atau filter pencarian Anda, atau tambahkan pertanyaan baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="p-4 sm:p-5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <div>
                @if($pertanyaans->onFirstPage())
                    <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed select-none">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $pertanyaans->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-[#741228] font-bold hover:bg-rose-50 transition">&laquo; Sebelumnya</a>
                @endif
            </div>

            <span class="px-3 py-1.5 text-gray-400 font-medium">Hal. {{ $pertanyaans->currentPage() }} dari {{ max(1, $pertanyaans->lastPage()) }}</span>

            <div>
                @if($pertanyaans->hasMorePages())
                    <a href="{{ $pertanyaans->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-[#741228] font-bold hover:bg-rose-50 transition">Selanjutnya &raquo;</a>
                @else
                    <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed select-none">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
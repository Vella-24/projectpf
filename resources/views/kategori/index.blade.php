@extends('layouts.superadmin')

@section('title', 'Kelola Kategori - ASK UBAYA')
@section('page_title', 'Kelola Kategori')

@section('content')
<div class="space-y-6">

    <!-- Header & Tombol Tambah Kategori -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Kelola Kategori</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Kelola daftar kategori pengetahuan ASK UBAYA. Kategori baru langsung tersedia saat Data Entry atau Super Admin membuat pertanyaan.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('kategori.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-[#741228] hover:bg-[#5e0d1f] text-white transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span> Tambah Kategori</span>
            </a>
        </div>
    </div>

    <!-- Alert Sukses / Error jika ada -->
    @if(session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center gap-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">{{ session('sukses') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center gap-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
            </svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabel Kategori -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70 text-[11px] font-extrabold uppercase tracking-wider text-gray-400">
                        <th class="py-4 px-6 w-16 text-center">NO</th>
                        <th class="py-4 px-6">NAMA KATEGORI</th>
                        <th class="py-4 px-6">DESKRIPSI</th>
                        <th class="py-4 px-6 text-center">JUMLAH PERTANYAAN</th>
                        <th class="py-4 px-6 text-center w-36">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-xs sm:text-sm">
                    @forelse($kategoris as $index => $kat)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-4 px-6 text-center text-gray-400 font-semibold">{{ $index + 1 }}</td>
                        <td class="py-4 px-6 font-bold text-gray-900">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-[#FFF5F6] border border-rose-100 text-[#741228] flex items-center justify-center font-bold text-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <span>{{ $kat->nama_kategori }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-500 max-w-md">
                            {{ $kat->deskripsi ?: '-' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FAF0F2] text-[#741228] border border-[#F5D0D6]">
                                {{ $kat->pertanyaans_count }} Pertanyaan
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('kategori.edit', $kat->id) }}" 
                                   class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition" 
                                   title="Ubah Kategori">
                                    Edit
                                </a>
                                <form action="{{ route('kategori.destroy', $kat->id) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Menghapus kategori juga akan berdampak pada pertanyaan di dalamnya.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-100 text-xs font-bold text-rose-700 hover:bg-rose-100 transition" 
                                            title="Hapus Kategori">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <p class="font-bold text-gray-700 text-sm">Belum Ada Kategori</p>
                            <p class="text-xs text-gray-400 mt-1">Klik tombol "+ Tambah Kategori" di atas untuk menambahkan kategori baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

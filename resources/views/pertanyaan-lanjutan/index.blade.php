@extends('layouts.data-entry')

@section('title', 'ASK UBAYA - Pertanyaan Mahasiswa')
@section('page_title', 'Pertanyaan Mahasiswa')

@section('content')

<div class="space-y-6 pt-2">

    <!-- HEADER HALAMAN -->
    <div>
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">
            Pertanyaan Mahasiswa
        </h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
            Kelola pertanyaan lanjutan yang dikirim mahasiswa melalui portal ASK UBAYA. Klik kartu di bawah untuk memfilter daftar pertanyaan secara instan.
        </p>
    </div>

    <!-- NOTIFIKASI SUKSES -->
    @if (session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    <!-- NOTIFIKASI ERROR -->
    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl text-sm font-medium flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 4 KARTU RINGKASAN INTERAKTIF / QUICK FILTER (Klik untuk filter instan seperti di Dashboard Data Entry) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">

        <!-- 1. TOTAL PERTANYAAN (SEMUA STATUS) -->
        @php
            $isSemuaAktif = !request()->filled('status');
        @endphp
        <a href="{{ route('pertanyaanLanjutan.index', array_merge(request()->except('status', 'page'), [])) }}" 
           data-filter-card
           data-status=""
           data-color="maroon"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isSemuaAktif ? 'border-[#741228] ring-2 ring-[#741228]/15 bg-rose-50/10 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#741228] flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#741228]/10 text-[#741228] {{ $isSemuaAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900 leading-none mb-1 group-hover:text-[#741228] transition">
                    {{ $totalPertanyaan }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Total pertanyaan</p>
            </div>
        </a>

        <!-- 2. BELUM DITANGANI (STATUS: BARU) -->
        @php
            $isBaruAktif = request('status') === 'baru';
        @endphp
        <a href="{{ route('pertanyaanLanjutan.index', array_merge(request()->except('status', 'page'), ['status' => 'baru'])) }}" 
           data-filter-card
           data-status="baru"
           data-color="amber"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isBaruAktif ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/20 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 {{ $isBaruAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-amber-600 leading-none mb-1 group-hover:text-amber-600 transition">
                    {{ $totalBaru }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Belum ditangani</p>
            </div>
        </a>

        <!-- 3. SEDANG DITANGANI (STATUS: SEDANG_DITANGANI) -->
        @php
            $isDiprosesAktif = request('status') === 'sedang_ditangani';
        @endphp
        <a href="{{ route('pertanyaanLanjutan.index', array_merge(request()->except('status', 'page'), ['status' => 'sedang_ditangani'])) }}" 
           data-filter-card
           data-status="sedang_ditangani"
           data-color="blue"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isDiprosesAktif ? 'border-blue-500 ring-2 ring-blue-500/20 bg-blue-50/20 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 {{ $isDiprosesAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-blue-600 leading-none mb-1 group-hover:text-blue-600 transition">
                    {{ $totalDiproses }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Sedang ditangani</p>
            </div>
        </a>

        <!-- 4. SUDAH DIJAWAB (STATUS: DIJAWAB) -->
        @php
            $isDijawabAktif = request('status') === 'dijawab';
        @endphp
        <a href="{{ route('pertanyaanLanjutan.index', array_merge(request()->except('status', 'page'), ['status' => 'dijawab'])) }}" 
           data-filter-card
           data-status="dijawab"
           data-color="emerald"
           class="bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between group hover:shadow-md cursor-pointer {{ $isDijawabAktif ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center transition group-hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="badge-aktif inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 {{ $isDijawabAktif ? '' : 'hidden' }}">
                    Aktif
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900 leading-none mb-1 group-hover:text-emerald-600 transition">
                    {{ $totalDijawab ?? 0 }}
                </h3>
                <p class="text-xs font-semibold text-gray-400">Sudah dijawab</p>
            </div>
        </a>

    </div>

    <!-- AREA DAFTAR PERTANYAAN (Dengan id area-konten-pertanyaan untuk update instan tanpa reload lambat) -->
    <div id="area-konten-pertanyaan" class="relative overflow-hidden bg-white rounded-3xl border border-gray-100 shadow-sm flex flex-col transition-all duration-150">

        <!-- Indikator Bar Tipis di bagian atas -->
        <div id="table-loader-bar" class="hidden absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#741228] via-[#e11d48] to-[#741228] z-20 animate-pulse"></div>

        <!-- JUDUL SECTION & STATUS AKTIF -->
        <div class="px-5 sm:px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center gap-2">
                    <span>Daftar Pertanyaan Masuk</span>
                    @if(request()->filled('status'))
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600">
                            Status: {{ request('status') === 'baru' ? 'Belum Ditangani' : (request('status') === 'sedang_ditangani' ? 'Sedang Ditangani' : (request('status') === 'dijawab' ? 'Sudah Dijawab' : ucwords(str_replace('_', ' ', request('status'))))) }}
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Pertanyaan tersedia untuk seluruh Data Entry. Hanya satu petugas yang dapat mengambil setiap pertanyaan.
                </p>
            </div>

            @if(request()->filled('status'))
                <div class="shrink-0">
                    <a href="{{ route('pertanyaanLanjutan.index', request()->except('status', 'page')) }}" 
                       class="btn-ajax-reset text-xs font-semibold text-[#741228] hover:text-[#5e0d1f] flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100/80 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Reset Filter Status</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- LIST PERTANYAAN -->
        <div class="divide-y divide-gray-100">

            @forelse ($pertanyaans as $item)

                <div class="p-5 sm:p-6 hover:bg-gray-50/60 transition">

                    <!-- BAGIAN ATAS -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                        <div class="flex-1 min-w-0">

                            <!-- STATUS DAN TANGGAL -->
                            <div class="flex flex-wrap items-center gap-2 mb-3">

                                @if ($item->status === 'baru')
                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                        Belum Ditangani
                                    </span>

                                @elseif ($item->status === 'sedang_ditangani')
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                        Sedang Ditangani
                                    </span>

                                @elseif ($item->status === 'dijawab')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                        Sudah Dijawab
                                    </span>

                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">
                                        Ditutup
                                    </span>
                                @endif

                                <span class="text-xs text-gray-400">
                                    {{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}
                                </span>

                            </div>

                            <!-- PERTANYAAN MAHASISWA -->
                            <h4 class="text-base sm:text-lg font-bold text-gray-900 leading-snug">
                                {{ $item->isi_pertanyaan }}
                            </h4>

                            <!-- FAQ TERKAIT -->
                            <div class="mt-3 bg-[#FFF7F8] border border-rose-100 rounded-2xl p-3.5">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-[#741228] mb-1">
                                    FAQ Terkait
                                </p>
                                <p class="text-sm text-gray-700">
                                    {{ $item->pertanyaan->judul_pertanyaan ?? 'FAQ tidak tersedia' }}
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- IDENTITAS DAN PETUGAS -->
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-gray-100 pt-4">

                        <div class="flex flex-col gap-1.5">

                            @if (!empty($item->nama_mahasiswa))
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <i class="fas fa-user text-gray-400 w-4"></i>
                                    <span>
                                        {{ $item->nama_mahasiswa }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-envelope text-gray-400 w-4"></i>
                                <span class="break-all">
                                    {{ $item->email_mahasiswa }}
                                </span>
                            </div>

                            @if ($item->data_entry_id)
                                <div class="flex items-center gap-2 text-xs text-blue-700 font-semibold">
                                    <i class="fas fa-user-check w-4"></i>
                                    <span>
                                        Ditangani oleh {{ $item->dataEntry->name ?? 'Data Entry' }}
                                    </span>
                                </div>
                            @endif

                        </div>

                        <!-- AKSI -->
                        <div class="shrink-0">

                            @if ($item->status === 'baru')

                                <form action="{{ route('pertanyaanLanjutan.ambil', $item->id) }}"
                                      method="POST">
                                    @csrf

                                    <button type="submit"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-xl font-bold text-xs transition shadow-sm">
                                        <i class="fas fa-hand-paper"></i>
                                        <span>Ambil Pertanyaan</span>
                                    </button>

                                </form>

                            @elseif ($item->status === 'sedang_ditangani'
                                && $item->data_entry_id == session('user.id'))

                                @if (
                                        $item->faqBaru &&
                                        $item->faqBaru->status === 'menunggu_validasi'
                                    )
                                        <span class="inline-flex items-center px-4 py-2.5 bg-amber-50 text-amber-700 rounded-xl text-xs font-bold">
                                            Menunggu Validasi
                                        </span>
                                    @else
                                        <a href="{{ route('pertanyaanLanjutan.jawab', $item->id) }}"
                                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-xl text-xs font-bold">
                                            <i class="fas fa-pen"></i>
                                            <span>{{ $item->faqBaru ? 'Lanjutkan Jawaban' : 'Jawab Pertanyaan' }}</span>
                                        </a>
                                    @endif

                            @elseif ($item->status === 'sedang_ditangani')

                                <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-xs font-semibold">
                                    <i class="fas fa-lock"></i>
                                    <span>Sudah Diambil</span>
                                </span>

                            @elseif ($item->status === 'dijawab')

                                <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-semibold">
                                    <i class="fas fa-check-double"></i>
                                    <span>Selesai Dijawab</span>
                                </span>

                            @else

                                <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-xs font-semibold">
                                    <span>Ditutup</span>
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <!-- EMPTY STATE -->
                <div class="py-16 px-5 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-50 rounded-2xl flex items-center justify-center mb-4 text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>

                    <h4 class="text-sm font-bold text-gray-700">
                        @if(request()->filled('status'))
                            Tidak ada pertanyaan dengan status "{{ request('status') === 'baru' ? 'Belum Ditangani' : (request('status') === 'sedang_ditangani' ? 'Sedang Ditangani' : (request('status') === 'dijawab' ? 'Sudah Dijawab' : ucwords(str_replace('_', ' ', request('status'))))) }}"
                        @else
                            Belum Ada Pertanyaan Mahasiswa
                        @endif
                    </h4>

                    <p class="text-xs text-gray-400 mt-2">
                        @if(request()->filled('status'))
                            Silakan pilih filter status lain atau reset filter untuk melihat seluruh pertanyaan.
                        @else
                            Pertanyaan lanjutan dari mahasiswa akan muncul di sini.
                        @endif
                    </p>

                    @if(request()->filled('status'))
                        <div class="mt-4">
                            <a href="{{ route('pertanyaanLanjutan.index') }}" 
                               class="btn-ajax-reset inline-flex items-center gap-1.5 px-4 py-2 bg-[#741228] text-white rounded-xl text-xs font-bold hover:bg-[#5e0d1f] transition shadow-xs">
                                <span>Tampilkan Semua Pertanyaan</span>
                            </a>
                        </div>
                    @endif
                </div>

            @endforelse

        </div>

        <!-- PAGINATION -->
        @if ($pertanyaans->hasPages())
            <div class="px-5 sm:px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                <div>
                    Menampilkan <span class="font-bold text-gray-800">{{ $pertanyaans->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-800">{{ $pertanyaans->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $pertanyaans->total() }}</span> pertanyaan
                </div>
                <div class="flex items-center gap-1.5">
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
        @endif

    </div>

</div>

@endsection

@section('scripts')
<script>
    // Fitur Instant Cache & Background Prefetching (0ms response, tanpa reload lambat seperti di dashboard)
    document.addEventListener('DOMContentLoaded', function () {
        const areaKonten = document.getElementById('area-konten-pertanyaan');
        if (!areaKonten) return;

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
        pageCache.set(currentKey, areaKonten.innerHTML);

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
                const newContent = doc.getElementById('area-konten-pertanyaan');
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
                areaKonten.innerHTML = pageCache.get(key);
                window.history.pushState({ status: statusBaru }, '', url);
                return;
            }

            // 3. JIKA BELUM ADA DI CACHE: Tampilkan bar progress merah halus di bagian atas
            showLoader(true);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('area-konten-pertanyaan');
                if (newContent) {
                    pageCache.set(key, newContent.innerHTML);
                    areaKonten.innerHTML = newContent.innerHTML;
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
                    } else if (color === 'blue') {
                        card.classList.add('border-blue-500', 'ring-blue-500/20', 'bg-blue-50/20');
                    } else if (color === 'emerald') {
                        card.classList.add('border-emerald-500', 'ring-emerald-500/20', 'bg-emerald-50/20');
                    }
                    card.classList.remove('border-gray-100');
                    if (badge) badge.classList.remove('hidden');
                } else {
                    card.classList.remove('ring-2', 'shadow-sm', 'border-[#741228]', 'ring-[#741228]/15', 'bg-rose-50/10', 'border-amber-500', 'ring-amber-500/20', 'bg-amber-50/20', 'border-blue-500', 'ring-blue-500/20', 'bg-blue-50/20', 'border-emerald-500', 'ring-emerald-500/20', 'bg-emerald-50/20');
                    card.classList.add('border-gray-100');
                    if (badge) badge.classList.add('hidden');
                }
            });
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

        // Event listener delegasi pada areaKonten (Reset filter & Pagination)
        areaKonten.addEventListener('click', function (e) {
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

        // Navigasi Back / Forward history browser
        window.addEventListener('popstate', function (e) {
            const targetUrl = window.location.href;
            const targetKey = getCacheKey(targetUrl);
            const status = (e.state && e.state.status) !== undefined ? e.state.status : (new URLSearchParams(window.location.search).get('status') || '');
            
            if (pageCache.has(targetKey)) {
                areaKonten.innerHTML = pageCache.get(targetKey);
                updateKartuAktif(status);
            } else {
                window.location.reload();
            }
        });
    });
</script>
@endsection

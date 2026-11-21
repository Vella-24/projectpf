@extends('layouts.validator')

@section('title', 'Validasi Konten - ASK UBAYA')
@section('page_title', 'Validasi Konten')

@php
    $pertanyaanItems = $pertanyaans->map(function($p) {
        $categoryName = $p->kategori->nama_kategori ?? 'Umum';
        $authorName = $p->user->name ?? 'Kontributor';
        $timeString = '-';
        if ($p->created_at) {
            if ($p->created_at->isToday()) {
                $timeString = 'Hari ini, ' . $p->created_at->format('H:i');
            } elseif ($p->created_at->isYesterday()) {
                $timeString = 'Kemarin, ' . $p->created_at->format('H:i');
            } else {
                $timeString = $p->created_at->format('d M, H:i');
            }
        }

        $isUmum = !$p->fakultas || $p->fakultas === 'umum';

        $riwayatTerbaru = $p->riwayatJawaban->first();
        $jawabanLama = $riwayatTerbaru ? $riwayatTerbaru->jawaban_lama : null;
        $hasRevisi = !empty($jawabanLama);
        $isPembaruan = $p->status_pembaruan === \App\Models\Pertanyaan::STATUS_PEMBARUAN_MENUNGGU;

        return [
            'id' => $p->id,
            'kategori' => $categoryName,
            'kategori_upper' => strtoupper($categoryName),
            'judul' => $p->judul_pertanyaan,
            'jawaban' => $p->isi_jawaban,
            'jawaban_lama' => $jawabanLama,
            'has_revisi' => $hasRevisi,
            'revisi_user' => $riwayatTerbaru && $riwayatTerbaru->user ? $riwayatTerbaru->user->name : null,
            'revisi_waktu' => $riwayatTerbaru && $riwayatTerbaru->created_at ? $riwayatTerbaru->created_at->isoFormat('D MMMM Y, HH:mm') : null,
            'total_revisi' => $p->riwayatJawaban ? $p->riwayatJawaban->count() : 0,
            'status' => $p->status,
            'status_pembaruan' => $p->status_pembaruan,
            'is_pembaruan' => $isPembaruan,
            'draft_jawaban' => $p->draft_jawaban,
            'catatan_pembaruan' => $p->catatan_pembaruan,
            'pembaruan_oleh' => $p->pembaruanDimintaOlehUser->name ?? 'Validator / Super Admin',
            'minta_pembaruan_url' => route('pertanyaan.mintaPembaruan', $p->id),
            'validator_id' => $p->validator_id,
            'validator_name' => $p->validator->name ?? null,
            'boleh_review' => ((int) $p->status === 1 || $isPembaruan)
            && $p->validator_id !== null
            && (int) $p->validator_id === (int) session('user.id'),
            'status_label' => $isPembaruan ? 'Pembaruan Diajukan' : match ((int) $p->status) {
                0 => 'Draft',
                1 => 'Menunggu',
                2 => 'Disetujui',
                3 => 'Perlu Revisi',
                default => 'Tidak Diketahui',
            },
            'user' => $authorName,
            'catatan' => $p->catatan_validator ?? '',
            'waktu' => $timeString,
            'update_url' => route('validator.updateStatus', $p->id),
            'fakultas' => $p->fakultas ?? 'umum',
            'nama_fakultas' => $p->nama_fakultas ?? 'Umum (Semua Mahasiswa)',
            'is_umum' => $isUmum,
        ];
    })->values();

    $selectedId = (int) request('selected', 0);

    $firstItem = $pertanyaanItems->firstWhere('id', $selectedId)
        ?? $pertanyaanItems->first();
@endphp

@section('content')
    <!-- Header Sub-Title (Counter badge 8 perlu ditinjau sudah dihapus) -->
    <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Validasi Konten</h2>
        <p class="text-sm text-gray-500 mt-1">
            Tinjau pengajuan aktif dan telusuri seluruh hasil validasi dalam satu tempat.
        </p>
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

    <!-- Split-Pane Layout (Master-Detail 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
        
        <!-- Kolom Kiri: Daftar Pengajuan (lg:col-span-5) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 flex flex-col">
            
            <!-- Search & Filter Controls -->
            <div class="space-y-3 mb-4">
                <!-- Search Input -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="searchInput" oninput="handleSearchInput(this.value)" onkeydown="handleSearchKey(event)"
                           value="{{ request('q') }}"
                           placeholder="Cari konten..."
                           class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition">
                </div>

                <!-- Status Filter Dropdown -->
                <div class="relative">
                    <select id="statusFilter" onchange="handleFilter(this.value)"
                            class="w-full appearance-none bg-gray-50/70 border border-gray-200 rounded-xl py-2.5 px-3.5 text-sm text-gray-700 font-medium focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition cursor-pointer">
                        <option value=1 {{ ($status ?? 'menunggu_validasi') === 'menunggu_validasi' ? 'selected' : '' }}>Perlu Ditinjau</option>
                        <option value=2 {{ ($status ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value=3 {{ ($status ?? '') === 'ditolak' ? 'selected' : '' }}>Perlu Direvisi</option>
                        <option value="semua" {{ ($status ?? '') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
            </div>

            <!-- List Card Container -->
            <div id="cardListContainer" class="space-y-2.5 max-h-[640px] overflow-y-auto pr-1">
                @forelse($pertanyaanItems as $index => $item)
                <div id="card-{{ $item['id'] }}" 
                     onclick="selectItem({{ $item['id'] }})"
                     data-id="{{ $item['id'] }}"
                     data-status="{{ $item['status'] }}"
                     data-judul="{{ strtolower($item['judul']) }}"
                     data-kategori="{{ strtolower($item['kategori']) }}"
                     data-user="{{ strtolower($item['user']) }}"
                     class="pertanyaan-card cursor-pointer p-4 rounded-xl transition-all duration-200 border {{ $firstItem && $firstItem['id'] == $item['id'] ? 'bg-[#FFF5F6] border-l-4 border-l-[#741228] border-t-rose-100 border-r-rose-100 border-b-rose-100 shadow-2xs' : 'bg-white hover:bg-gray-50/80 border-gray-100' }}">
                    
                    <!-- Top Meta: Status pill & Time -->
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ (int)$item['status'] === 1 ? 'text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md' : ((int)$item['status'] === 2 ? 'text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md' : 'text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md') }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ (int)$item['status'] === 1 ? 'bg-amber-500' : ((int)$item['status'] === 2 ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                                {{ $item['status_label'] }}
                            </span>
                            @if($item['is_pembaruan'])
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Kebijakan Baru
                                </span>
                            @elseif($item['has_revisi'])
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Revisi
                                </span>
                            @endif
                        </div>
                        <span class="text-[11px] text-gray-400">{{ $item['waktu'] }}</span>
                    </div>

                    <!-- Title -->
                    <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mb-1">
                        {{ $item['judul'] }}
                    </h3>

                    <!-- Subtitle: Kategori & Pengaju -->
                    <p class="text-xs text-gray-400 truncate">
                        {{ $item['kategori'] }} &bull; oleh <span class="text-gray-600">{{ $item['user'] }}</span>
                    </p>
                    <!-- Informasi Validator yang mengambil review -->
                    <div class="mt-2">

                        @if ($item['validator_id'] == null)

                            <span class="text-xs text-amber-600 font-semibold">
                                Belum direview
                            </span>

                            <form action="{{ route('validator.ambilReview', $item['id']) }}"
                                method="POST"
                                class="mt-2"
                                onclick="event.stopPropagation()">

                                @csrf
                                <input type="hidden" name="page" value="{{ $pertanyaans->currentPage() }}">
                                <button type="submit"
                                        class="text-xs font-bold text-white bg-[#741228] px-3 py-1.5 rounded-lg hover:bg-[#5e0d1f]">
                                    Ambil untuk Direview
                                </button>

                            </form>

                        @else

                            <span class="text-xs text-blue-600 font-semibold">
                                Sedang direview oleh {{ $item['validator_name'] ?? 'Validator' }}
                            </span>

                        @endif

                    </div>
                </div>
                @empty
                <div class="text-center py-12 px-4 text-gray-400">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-xs font-semibold text-gray-500">Tidak ada pengajuan</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Tidak ditemukan pengajuan dengan status ini.</p>
                </div>
                @endforelse

                <!-- Empty Search / Filter State (client-side) -->
                <div id="noResults" class="hidden text-center py-8 text-gray-400 text-sm">
                    Tidak ada konten yang sesuai dengan pencarian.
                </div>
            </div>

            <!-- Pagination (Next hanya aktif jika > 10 data / hasMorePages) -->
            <div class="flex items-center justify-between pt-4 mt-3 border-t border-gray-100 text-xs">
                <div>
                    @if ($pertanyaans->onFirstPage())
                        <span class="text-gray-300 cursor-not-allowed select-none">&laquo; Sebelumnya</span>
                    @else
                        <a href="{{ $pertanyaans->previousPageUrl() }}" class="font-bold text-[#741228] hover:underline">&laquo; Sebelumnya</a>
                    @endif
                </div>
                <span class="text-gray-400">Hal. {{ $pertanyaans->currentPage() }} dari {{ max(1, $pertanyaans->lastPage()) }}</span>
                <div>
                    @if ($pertanyaans->hasMorePages())
                        <a href="{{ $pertanyaans->nextPageUrl() }}" class="font-bold text-[#741228] hover:underline">Selanjutnya &raquo;</a>
                    @else
                        <span class="text-gray-300 cursor-not-allowed select-none">Selanjutnya &raquo;</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Review & Form Keputusan (lg:col-span-7) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 sticky top-20">
            @if($firstItem)
            <!-- Category Tag & Status Badge -->
            <div class="flex items-center justify-between gap-3 mb-2">
                <span id="detailKategori" class="text-xs font-extrabold uppercase tracking-wider text-[#741228]">
                    {{ $firstItem['kategori_upper'] }}
                </span>
                <span id="detailStatusBadge" class="inline-flex items-center gap-1.5 text-xs font-semibold {{ (int)$firstItem['status'] === 1 ? 'text-amber-700 bg-amber-50 border-amber-200/60' : ((int)$firstItem['status'] === 2 ? 'text-emerald-700 bg-emerald-50 border-emerald-200/60' : 'text-rose-700 bg-rose-50 border-rose-200/60') }} px-2.5 py-1 rounded-full border">
                    <span id="detailStatusDot" class="w-1.5 h-1.5 rounded-full {{ (int)$firstItem['status'] === 1 ? 'bg-amber-500' : ((int)$firstItem['status'] === 2 ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                    <span id="detailStatus">{{ $firstItem['status_label'] }}</span>
                </span>
            </div>

            <!-- Big Title -->
            <h2 id="detailJudul" class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug mb-2">
                {{ $firstItem['judul'] }}
            </h2>

            <!-- Sub Metadata -->
            <p id="detailMeta" class="text-xs text-gray-400 mb-4">
                Diajukan oleh <span class="text-gray-700 font-medium">{{ $firstItem['user'] }}</span> &bull; {{ $firstItem['waktu'] }}
            </p>

            <!-- Info Box: Cakupan Sasaran Fakultas -->
            <div id="detailFakultasBox" class="mb-5 flex items-start gap-3 p-3.5 rounded-xl border {{ $firstItem['is_umum'] ? 'bg-blue-50 border-blue-200' : 'bg-amber-50 border-amber-200' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $firstItem['is_umum'] ? 'bg-blue-100 text-blue-600' : 'bg-amber-100 text-amber-600' }}">
                    @if($firstItem['is_umum'])
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-wider {{ $firstItem['is_umum'] ? 'text-blue-700' : 'text-amber-700' }} mb-0.5">
                        Cakupan Sasaran
                    </p>
                    <p id="detailFakultasLabel" class="text-xs font-bold {{ $firstItem['is_umum'] ? 'text-blue-800' : 'text-amber-800' }}">
                        {{ $firstItem['is_umum'] ? '🌐 Umum — Semua Mahasiswa UBAYA' : '🏛️ Khusus ' . $firstItem['nama_fakultas'] }}
                    </p>
                    <p id="detailFakultasDesc" class="text-[11px] mt-0.5 {{ $firstItem['is_umum'] ? 'text-blue-600' : 'text-amber-700' }}">
                        {{ $firstItem['is_umum'] ? 'Berlaku untuk seluruh mahasiswa UBAYA dari semua fakultas.' : 'Pastikan isi jawaban sesuai kebijakan ' . $firstItem['nama_fakultas'] . '.' }}
                    </p>
                </div>
            </div>

            <!-- Info Banner Jika Pembaruan Kebijakan Diajukan -->
            <div id="detailPembaruanBanner" class="{{ ($firstItem['is_pembaruan'] ?? false) ? '' : 'hidden' }} p-4 bg-purple-50 border border-purple-200 rounded-2xl text-xs space-y-2 text-purple-950 mb-5 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="font-extrabold flex items-center gap-1.5 text-purple-900 text-sm">
                        <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pembaruan Jawaban Kebijakan Baru Diajukan
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-200 text-purple-900">
                        Menunggu Validasi
                    </span>
                </div>
                <p class="text-xs text-purple-900 bg-white/70 p-2.5 rounded-xl border border-purple-100">
                    Arahan perubahan: <strong id="detailPembaruanCatatan">{{ $firstItem['catatan_pembaruan'] ?? '' }}</strong>
                </p>
                <div class="text-[11px] text-purple-700 flex items-center justify-between flex-wrap gap-2 pt-0.5">
                    <span>Diminta oleh: <strong id="detailPembaruanOleh">{{ $firstItem['pembaruan_oleh'] ?? '' }}</strong></span>
                    <span class="text-emerald-700 font-bold">✓ Jawaban lama masih tayang di mahasiswa</span>
                </div>
            </div>

            <!-- Informasi siapa yang mengambil review -->
            <div id="detailValidatorInfo" class="mb-5 text-sm font-semibold"></div>

            <!-- Section: Jawaban & Perbandingan Revisi -->
            <div class="mb-6 space-y-4">
                <!-- Info Banner Jika Ada Revisi -->
                <div id="detailRevisiBanner" class="{{ ($firstItem['has_revisi'] ?? false) ? '' : 'hidden' }} p-3.5 bg-amber-50/80 border border-amber-200 rounded-xl text-xs flex items-start gap-2.5 text-amber-900">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <span class="font-bold">Pertanyaan ini memiliki revisi:</span>
                        <span id="detailRevisiMeta" class="text-amber-800 ml-1">
                            @if(!empty($firstItem['revisi_user']))
                                Diedit oleh <strong>{{ $firstItem['revisi_user'] }}</strong> ({{ $firstItem['revisi_waktu'] }})
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Box Jawaban Sebelumnya (Tampil jika ada revisi) -->
                <div id="detailRevisiBox" class="{{ ($firstItem['has_revisi'] ?? false) ? '' : 'hidden' }}">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-bold text-rose-700 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            JAWABAN SEBELUMNYA (VERSI LAMA)
                        </h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            Sebelum Revisi
                        </span>
                    </div>
                    <div id="detailJawabanLama" class="text-sm leading-relaxed text-gray-600 bg-rose-50/20 p-4 rounded-xl border border-rose-200/70 whitespace-pre-line">
                        {{ $firstItem['jawaban_lama'] ?? '' }}
                    </div>
                </div>

                <!-- Box Jawaban Baru / Yang Diajukan -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 id="detailJudulJawaban" class="text-xs font-bold {{ ($firstItem['has_revisi'] ?? false) ? 'text-emerald-700' : 'text-gray-400' }} uppercase tracking-wider flex items-center gap-1.5">
                            <span id="detailJudulJawabanText">{{ ($firstItem['has_revisi'] ?? false) ? 'JAWABAN BARU (HASIL REVISI)' : 'JAWABAN YANG DIAJUKAN' }}</span>
                        </h4>
                        <span id="detailBadgeJawabanBaru" class="{{ ($firstItem['has_revisi'] ?? false) ? '' : 'hidden' }} text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Versi Terbaru
                        </span>
                    </div>
                    <div id="detailJawaban" class="text-sm leading-relaxed text-gray-800 {{ ($firstItem['has_revisi'] ?? false) ? 'bg-emerald-50/20 border-emerald-200/70' : 'bg-gray-50/50 border-gray-100' }} p-4 rounded-xl border whitespace-pre-line">
                        {{ $firstItem['jawaban'] }}
                    </div>
                </div>
            </div>
            <!-- Form Keputusan Validator (Menggunakan Route updateStatus yang ada) -->
            <form id="formReview" action="{{ $firstItem['update_url'] }}" method="POST" class="hidden">
                @csrf
                @method('PUT')
                
                <!-- Hidden input status: akan diisi 2 (disetujui) atau 3 (ditolak) via button -->
                <input type="hidden" name="status" id="inputStatus" value="2">
                <input type="hidden" name="redirect_status" value="{{ $status ?? 1 }}">

                <!-- Section: Catatan untuk Kontributor -->
                <div class="mb-6">
                    <label for="catatan_validator" class="block text-sm font-bold text-gray-800 mb-2">
                        Catatan untuk kontributor
                    </label>
                    <textarea name="catatan_validator" id="detailCatatan" rows="4"
                              placeholder="Tambahkan catatan jika konten perlu direvisi..."
                              class="w-full p-3.5 bg-gray-50/60 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition resize-y">{{ $firstItem['catatan'] }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div id="decisionButtons" class="flex items-center justify-end gap-3 pt-2">
                    <!-- Button Minta Revisi (Ditolak) -->
                    <button type="button" onclick="submitDecision(3)"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-[#FFF1F2] text-[#9F1239] hover:bg-[#FFE4E6] border border-[#FECDD3] transition cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4 text-[#9F1239]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Minta Revisi</span>
                    </button>

                    <!-- Button Setujui & Terbitkan (Disetujui) -->
                    <button type="button" onclick="submitDecision(2)"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm bg-[#741228] hover:bg-[#5e0d1f] text-white transition cursor-pointer shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Setujui & Terbitkan</span>
                    </button>
                </div>
                <p id="reviewWarning" class="hidden text-sm text-amber-700 mt-3">
                    Kamu tidak dapat memvalidasi pertanyaan ini karena sedang direview oleh Validator lain.
                </p>
            </form>

            <!-- Box Minta Pembaruan Kebijakan (Hanya untuk FAQ yang sudah disetujui / status 2 dan tidak sedang dalam pembaruan) -->
            <div id="boxMintaPembaruan" class="{{ (int)($firstItem['status'] ?? 0) === 2 && !($firstItem['is_pembaruan'] ?? false) && ($firstItem['status_pembaruan'] ?? null) !== 'perlu_pembaruan' ? '' : 'hidden' }} mt-6 pt-5 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h4 class="text-xs font-bold text-gray-800">Ada Perubahan Kebijakan Baru?</h4>
                    <p class="text-[11px] text-gray-400">Instruksikan Data Entry untuk memperbarui jawaban FAQ yang sedang tayang ini.</p>
                </div>
                <button type="button" onclick="openModalMintaPembaruan()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition cursor-pointer shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Minta Pembaruan</span>
                </button>
            </div>
            @else
            <!-- Placeholder saat tidak ada data -->
            <div class="h-full min-h-[460px] flex flex-col items-center justify-center text-center p-8 text-gray-400">
                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="font-bold text-gray-700 text-sm mb-1">Tidak ada pengajuan yang dipilih</p>
                <p class="text-xs text-gray-400 max-w-xs">Gunakan filter status atau kata kunci pencarian di sisi kiri untuk melihat data pengajuan lainnya.</p>
            </div>
            @endif
        </div>

    </div>

    <!-- Modal Minta Pembaruan Kebijakan -->
    <div id="modalMintaPembaruan" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-gray-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900">Minta Pembaruan Kebijakan</h3>
                </div>
                <button type="button" onclick="closeModalMintaPembaruan()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">✕</button>
            </div>
            <form id="formMintaPembaruan" action="{{ $firstItem ? $firstItem['minta_pembaruan_url'] : '' }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="modalCatatanPembaruan" class="block text-xs font-bold text-gray-800 mb-1">
                        Catatan Arahan Kebijakan Baru <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-gray-400 mb-2">Tuliskan instruksi atau poin kebijakan baru yang harus disesuaikan oleh Data Entry.</p>
                    <textarea id="modalCatatanPembaruan" name="catatan_pembaruan" rows="4" required placeholder="Contoh: Perbarui jawaban sesuai SK Rektor No. 12/2026 mengenai perpanjangan masa pembayaran SPP..." class="w-full p-3.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:bg-white focus:outline-none focus:border-[#741228] transition leading-relaxed"></textarea>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 font-medium leading-relaxed">
                    ⚠️ <strong>Informasi Alur:</strong> Notifikasi "Perlu Pembaruan" akan dikirimkan ke Data Entry dan tombol edit FAQ ini otomatis terbuka. Jawaban lama <u>tetap tayang</u> di halaman mahasiswa sampai jawaban baru Anda setujui.
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModalMintaPembaruan()" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#741228] hover:bg-[#5e0d1f] shadow-sm">Kirim ke Data Entry</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Data pertanyaan dari backend
    const items = @json($pertanyaanItems);
    let activeId = @json($firstItem['id'] ?? null);

    // ID Validator yang sedang login
    const currentValidatorId = @json(session('user.id'));

    // Pilih item untuk ditampilkan di kolom detail kanan
    function selectItem(id) {
        const item = items.find(i => i.id === id);
        if (!item) return;

        activeId = id;

        const elKategori = document.getElementById('detailKategori');
        if (elKategori) elKategori.textContent = item.kategori_upper;

        const elStatus = document.getElementById('detailStatus');
        if (elStatus) elStatus.textContent = item.status_label;

        const elJudul = document.getElementById('detailJudul');
        if (elJudul) elJudul.textContent = item.judul;

        const elMeta = document.getElementById('detailMeta');
        if (elMeta) elMeta.innerHTML = `Diajukan oleh <span class="text-gray-700 font-medium">${item.user}</span> &bull; ${item.waktu}`;

        // Update info box cakupan sasaran fakultas
        const fakultasBox = document.getElementById('detailFakultasBox');
        const fakultasLabel = document.getElementById('detailFakultasLabel');
        const fakultasDesc = document.getElementById('detailFakultasDesc');

        if (fakultasBox && fakultasLabel && fakultasDesc) {
            const isUmum = item.is_umum;

            // Update warna box
            fakultasBox.className = `mb-5 flex items-start gap-3 p-3.5 rounded-xl border ${isUmum ? 'bg-blue-50 border-blue-200' : 'bg-amber-50 border-amber-200'}`;

            // Update icon container
            const iconDiv = fakultasBox.querySelector('div:first-child');
            if (iconDiv) {
                iconDiv.className = `w-8 h-8 rounded-lg flex items-center justify-center shrink-0 ${isUmum ? 'bg-blue-100 text-blue-600' : 'bg-amber-100 text-amber-600'}`;
            }

            // Update label text dan warna
            fakultasLabel.className = `text-xs font-bold ${isUmum ? 'text-blue-800' : 'text-amber-800'}`;
            fakultasLabel.textContent = isUmum
                ? '🌐 Umum — Semua Mahasiswa UBAYA'
                : '🏛️ Khusus ' + item.nama_fakultas;

            // Update desc text dan warna
            fakultasDesc.className = `text-[11px] mt-0.5 ${isUmum ? 'text-blue-600' : 'text-amber-700'}`;
            fakultasDesc.textContent = isUmum
                ? 'Berlaku untuk seluruh mahasiswa UBAYA dari semua fakultas.'
                : 'Pastikan isi jawaban sesuai kebijakan ' + item.nama_fakultas + '.';
        }

        const elJawaban = document.getElementById('detailJawaban');
        if (elJawaban) elJawaban.textContent = item.jawaban;

        const elCatatan = document.getElementById('detailCatatan');
        if (elCatatan) elCatatan.value = item.catatan || '';

        <!-- Update perbandingan revisi / pembaruan kebijakan -->
        const revisiBanner = document.getElementById('detailRevisiBanner');
        const revisiMeta = document.getElementById('detailRevisiMeta');
        const revisiBox = document.getElementById('detailRevisiBox');
        const elJawabanLama = document.getElementById('detailJawabanLama');
        const elJudulJawaban = document.getElementById('detailJudulJawaban');
        const elJudulJawabanText = document.getElementById('detailJudulJawabanText');
        const elBadgeJawabanBaru = document.getElementById('detailBadgeJawabanBaru');
        const pembaruanBanner = document.getElementById('detailPembaruanBanner');
        const pembaruanCatatan = document.getElementById('detailPembaruanCatatan');
        const pembaruanOleh = document.getElementById('detailPembaruanOleh');
        const boxMintaPembaruan = document.getElementById('boxMintaPembaruan');
        const formMintaPembaruan = document.getElementById('formMintaPembaruan');

        if (item.is_pembaruan) {
            if (pembaruanBanner) pembaruanBanner.classList.remove('hidden');
            if (pembaruanCatatan) pembaruanCatatan.textContent = item.catatan_pembaruan || '-';
            if (pembaruanOleh) pembaruanOleh.textContent = item.pembaruan_oleh || 'Validator / Super Admin';

            if (revisiBanner) revisiBanner.classList.add('hidden');
            if (revisiBox) revisiBox.classList.remove('hidden');
            if (elJawabanLama) elJawabanLama.textContent = item.jawaban;
            
            const titleLama = revisiBox.querySelector('h4');
            if (titleLama) titleLama.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> JAWABAN LAMA (SEDANG TAYANG DI MAHASISWA)';
            const badgeLama = revisiBox.querySelector('span');
            if (badgeLama) {
                badgeLama.textContent = 'Sedang Tayang';
                badgeLama.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
            }

            if (elJudulJawaban) elJudulJawaban.className = "text-xs font-bold text-purple-700 uppercase tracking-wider flex items-center gap-1.5";
            if (elJudulJawabanText) elJudulJawabanText.textContent = "JAWABAN BARU (DIAJUKAN SESUAI KEBIJAKAN BARU)";
            if (elBadgeJawabanBaru) {
                elBadgeJawabanBaru.classList.remove('hidden');
                elBadgeJawabanBaru.textContent = "Diajukan Baru";
                elBadgeJawabanBaru.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-200';
            }
            if (elJawaban) {
                elJawaban.textContent = item.draft_jawaban || item.jawaban;
                elJawaban.className = "text-sm leading-relaxed text-gray-800 bg-purple-50/20 border-purple-200/70 p-4 rounded-xl border whitespace-pre-line";
            }
        } else if (item.has_revisi && item.jawaban_lama) {
            if (pembaruanBanner) pembaruanBanner.classList.add('hidden');
            if (revisiBanner) revisiBanner.classList.remove('hidden');
            if (revisiMeta) revisiMeta.innerHTML = `Diedit oleh <strong>${item.revisi_user || 'Kontributor'}</strong> (${item.revisi_waktu || '-'})`;
            if (revisiBox) revisiBox.classList.remove('hidden');
            if (elJawabanLama) elJawabanLama.textContent = item.jawaban_lama;
            
            const titleLama = revisiBox.querySelector('h4');
            if (titleLama) titleLama.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> JAWABAN SEBELUMNYA (VERSI LAMA)';
            const badgeLama = revisiBox.querySelector('span');
            if (badgeLama) {
                badgeLama.textContent = 'Sebelum Revisi';
                badgeLama.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200';
            }

            if (elJudulJawaban) elJudulJawaban.className = "text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5";
            if (elJudulJawabanText) elJudulJawabanText.textContent = "JAWABAN BARU (HASIL REVISI)";
            if (elBadgeJawabanBaru) {
                elBadgeJawabanBaru.classList.remove('hidden');
                elBadgeJawabanBaru.textContent = "Versi Terbaru";
                elBadgeJawabanBaru.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
            }
            if (elJawaban) {
                elJawaban.textContent = item.jawaban;
                elJawaban.className = "text-sm leading-relaxed text-gray-800 bg-emerald-50/20 border-emerald-200/70 p-4 rounded-xl border whitespace-pre-line";
            }
        } else {
            if (pembaruanBanner) pembaruanBanner.classList.add('hidden');
            if (revisiBanner) revisiBanner.classList.add('hidden');
            if (revisiBox) revisiBox.classList.add('hidden');
            if (elJudulJawaban) elJudulJawaban.className = "text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5";
            if (elJudulJawabanText) elJudulJawabanText.textContent = "JAWABAN YANG DIAJUKAN";
            if (elBadgeJawabanBaru) elBadgeJawabanBaru.classList.add('hidden');
            if (elJawaban) {
                elJawaban.textContent = item.jawaban;
                elJawaban.className = "text-sm leading-relaxed text-gray-700 bg-gray-50/50 border-gray-100 p-4 rounded-xl border whitespace-pre-line";
            }
        }

        // Tampilkan tombol Minta Pembaruan jika disetujui (status 2) dan tidak sedang dalam pembaruan
        if (boxMintaPembaruan) {
            const bolehMinta = Number(item.status) === 2 && !item.is_pembaruan && item.status_pembaruan !== 'perlu_pembaruan';
            boxMintaPembaruan.classList.toggle('hidden', !bolehMinta);
        }
        if (formMintaPembaruan) {
            formMintaPembaruan.action = item.minta_pembaruan_url;
        }

        const formReview = document.getElementById('formReview');
        const validatorInfo = document.getElementById('detailValidatorInfo');

        // Cek apakah FAQ sudah diambil oleh Validator yang login
        const bolehReview = item.boleh_review === true;
        

        // Atur form sesuai FAQ yang diklik
        if (formReview) {
            formReview.action = item.update_url;

            // Sembunyikan seluruh form jika belum mengambil review
            formReview.classList.toggle('hidden', item.boleh_review !== true);
        }

        // Tampilkan informasi status review
        if (validatorInfo) {
            if (item.is_pembaruan) {
                if (bolehReview) {
                    validatorInfo.textContent = 'Kamu sedang mereview pengajuan pembaruan kebijakan ini.';
                    validatorInfo.className = 'mb-5 text-sm font-semibold text-purple-700';
                } else if (item.validator_id != null) {
                    validatorInfo.textContent = 'Sedang direview oleh ' + (item.validator_name || 'Validator');
                    validatorInfo.className = 'mb-5 text-sm font-semibold text-blue-600';
                } else {
                    validatorInfo.textContent = 'Pengajuan pembaruan kebijakan baru. Klik Ambil untuk Direview terlebih dahulu.';
                    validatorInfo.className = 'mb-5 text-sm font-semibold text-amber-600';
                }
            } else if (Number(item.status) !== 1) {
                validatorInfo.textContent = 'FAQ ini sudah selesai divalidasi.';
                validatorInfo.className = 'mb-5 text-sm font-semibold text-gray-500';
            } else if (bolehReview) {
                validatorInfo.textContent = 'Kamu sedang mereview pertanyaan ini.';
                validatorInfo.className = 'mb-5 text-sm font-semibold text-emerald-600';
            } else if (item.validator_id != null) {
                validatorInfo.textContent = 'Sedang direview oleh ' + (item.validator_name || 'Validator');
                validatorInfo.className = 'mb-5 text-sm font-semibold text-blue-600';
            } else {
                validatorInfo.textContent = 'Belum ada Validator yang mengambil review. Klik Ambil untuk Direview terlebih dahulu.';
                validatorInfo.className = 'mb-5 text-sm font-semibold text-amber-600';
            }
        }

        // Status Badge styling
        const badge = document.getElementById('detailStatusBadge');
        const dot = document.getElementById('detailStatusDot');
        if (badge && dot) {
            if (item.is_pembaruan) {
                badge.className = "inline-flex items-center gap-1.5 text-xs font-semibold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-full border border-purple-200/60";
                dot.className = "w-1.5 h-1.5 rounded-full bg-purple-500";
            } else if (Number(item.status) === 1) {
                badge.className = "inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200/60";
                dot.className = "w-1.5 h-1.5 rounded-full bg-amber-500";
            } else if (Number(item.status) === 2) {
                badge.className = "inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60";
                dot.className = "w-1.5 h-1.5 rounded-full bg-emerald-500";
            } else if (Number(item.status) === 3) {
                badge.className = "inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200/60";
                dot.className = "w-1.5 h-1.5 rounded-full bg-rose-500";
            }
        }

        // Update styling kartu aktif di list kiri
        document.querySelectorAll('.pertanyaan-card').forEach(el => {
            el.classList.remove('bg-[#FFF5F6]', 'border-l-4', 'border-l-[#741228]', 'border-t-rose-100', 'border-r-rose-100', 'border-b-rose-100', 'shadow-2xs');
            el.classList.add('bg-white', 'hover:bg-gray-50/80', 'border-gray-100');
        });

        const currentCard = document.getElementById('card-' + id);
        if (currentCard) {
            currentCard.classList.remove('bg-white', 'hover:bg-gray-50/80', 'border-gray-100');
            currentCard.classList.add('bg-[#FFF5F6]', 'border-l-4', 'border-l-[#741228]', 'border-t-rose-100', 'border-r-rose-100', 'border-b-rose-100', 'shadow-2xs');
        }
    }

    // Submit form review keputusan
    function submitDecision(status) {
        const itemAktif = items.find(i => i.id === activeId);

        if (!itemAktif || itemAktif.boleh_review !== true) {
            alert('Ambil pertanyaan untuk direview terlebih dahulu.');
            return;
        }

        const formReview = document.getElementById('formReview');
        if (!formReview) return;

        document.getElementById('inputStatus').value = status;
        
        if (status === 'ditolak' || status === 3 || status === '3') {
            const catatan = document.getElementById('detailCatatan')?.value.trim();
            if (!catatan) {
                if (!confirm('Anda belum memberikan catatan revisi untuk kontributor. Apakah Anda yakin ingin meminta revisi tanpa catatan?')) {
                    document.getElementById('detailCatatan')?.focus();
                    return;
                }
            }
        }
        
        formReview.submit();
    }

    // Filter status via server request sehingga pagination 10 data per halaman sinkron
    function handleFilter(statusValue) {
        const url = new URL(window.location.origin + window.location.pathname);
        url.searchParams.set('status', statusValue);
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value.trim() !== '') {
            url.searchParams.set('q', searchInput.value.trim());
        }
        window.location.href = url.toString();
    }

    // Full search on Enter
    function handleSearchKey(event) {
        if (event.key === 'Enter') {
            const url = new URL(window.location.origin + window.location.pathname);
            const filterVal = document.getElementById('statusFilter')?.value || 'menunggu_validasi';
            url.searchParams.set('status', filterVal);
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value.trim() !== '') {
                url.searchParams.set('q', searchInput.value.trim());
            }
            window.location.href = url.toString();
        }
    }

    // Instant local filter on typing
    function handleSearchInput(keyword) {
        const kw = keyword.toLowerCase().trim();
        let visibleCount = 0;
        let firstVisibleId = null;

        document.querySelectorAll('.pertanyaan-card').forEach(card => {
            const judul = card.dataset.judul || '';
            const kategori = card.dataset.kategori || '';
            const user = card.dataset.user || '';
            const id = parseInt(card.dataset.id);

            const match = !kw || judul.includes(kw) || kategori.includes(kw) || user.includes(kw);
            if (match) {
                card.style.display = 'block';
                visibleCount++;
                if (!firstVisibleId) firstVisibleId = id;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResults');
        if (noResults) {
            noResults.classList.toggle('hidden', visibleCount > 0);
        }

        if (firstVisibleId && firstVisibleId !== activeId) {
            selectItem(firstVisibleId);
        }

        
    }

    function openModalMintaPembaruan() {
        const m = document.getElementById('modalMintaPembaruan');
        if (m) m.classList.remove('hidden');
    }

    function closeModalMintaPembaruan() {
        const m = document.getElementById('modalMintaPembaruan');
        if (m) m.classList.add('hidden');
    }

    if (activeId !== null) {
        selectItem(activeId);
    }
</script>
@endsection

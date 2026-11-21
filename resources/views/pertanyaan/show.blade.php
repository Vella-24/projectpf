@extends(session('user.role') === 'super_admin' ? 'layouts.superadmin' : (session('user.role') === 'validator' ? 'layouts.validator' : 'layouts.data-entry'))

@section('title', 'ASK UBAYA - Detail Pertanyaan')
@section('page_title', 'Detail Pertanyaan')

@php
    $backRoute = route('pertanyaan.index');
    if (session('user.role') === 'super_admin') {
        $backRoute = route('superadmin.pertanyaan.index');
    } elseif (session('user.role') === 'validator') {
        $backRoute = route('validator.index');
    }

    $editRoute = session('user.role') === 'super_admin' 
        ? route('superadmin.pertanyaan.edit', $pertanyaan->id) 
        : route('pertanyaan.edit', $pertanyaan->id);

    $destroyRoute = session('user.role') === 'super_admin' 
        ? route('superadmin.pertanyaan.destroy', $pertanyaan->id) 
        : route('pertanyaan.destroy', $pertanyaan->id);
@endphp

@section('content')
<div class="space-y-6 pt-2">

    <!-- Sub-Bar Navigasi & Aksi -->
    <div class="flex items-center justify-between pb-2">
        <a href="{{ $backRoute }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>

        <div class="flex items-center gap-3">
            @if(session('user.role') === 'data_entry')
                @if($pertanyaan->is_perlu_pembaruan)
                    <a href="{{ route('pertanyaan.edit', $pertanyaan->id) }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-xs font-bold transition shadow-sm hover:shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Perbarui Jawaban</span>
                    </a>
                @elseif($pertanyaan->isLockedForDataEntry())
                    <span class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-100 text-gray-500 rounded-2xl text-xs font-bold border border-gray-200 cursor-not-allowed" 
                          title="Terkunci: Pertanyaan sudah disetujui & tayang di halaman mahasiswa.">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Terkunci (Tayang di Mahasiswa)</span>
                    </span>
                @else
                    <!-- Edit Normal -->
                    <a href="{{ $editRoute }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-sm hover:shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit Pertanyaan</span>
                    </a>

                    <!-- Hapus Normal -->
                    <form action="{{ $destroyRoute }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-2xl text-xs font-bold transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus</span>
                        </button>
                    </form>
                @endif
            @else
                <!-- Validator / Super Admin Actions -->
                @if((int)$pertanyaan->status === 2 && $pertanyaan->status_pembaruan !== \App\Models\Pertanyaan::STATUS_PEMBARUAN_PERLU)
                    <button type="button" onclick="openModalMintaPembaruan()" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-xs font-bold transition shadow-sm hover:shadow cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Minta Pembaruan Kebijakan</span>
                    </button>
                @endif

                @if(session('user.role') === 'super_admin')
                    <a href="{{ $editRoute }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-2xl text-xs font-bold transition">
                        <span>Edit</span>
                    </a>
                    <form action="{{ $destroyRoute }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-2xl text-xs font-bold transition cursor-pointer">
                            <span>Hapus</span>
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- Judul Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
            Detail Pengetahuan
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
            Informasi lengkap pertanyaan, jawaban terverifikasi, status validasi, dan riwayat revisi.
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

    <!-- Alert Pembaruan Kebijakan: Perlu Pembaruan -->
    @if($pertanyaan->is_perlu_pembaruan)
        <div class="bg-amber-50 border-2 border-amber-300 text-amber-950 p-5 rounded-3xl text-xs shadow-sm flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="space-y-1.5 flex-1">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="font-extrabold text-sm text-amber-950">Instruksi Pembaruan Kebijakan Baru:</span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900">Perlu Pembaruan</span>
                </div>
                <p class="text-xs text-amber-900 font-semibold leading-relaxed bg-amber-100/60 p-3 rounded-xl border border-amber-200">
                    {{ $pertanyaan->catatan_pembaruan }}
                </p>
                <div class="text-[11px] text-amber-800 font-medium pt-1 flex items-center justify-between flex-wrap gap-2">
                    <span>
                        Diminta oleh: <strong>{{ $pertanyaan->pembaruanDimintaOlehUser->name ?? 'Validator / Super Admin' }}</strong>
                        @if($pertanyaan->pembaruan_diminta_pada)
                            &bull; {{ \Carbon\Carbon::parse($pertanyaan->pembaruan_diminta_pada)->isoFormat('D MMMM Y, HH:mm') }}
                        @endif
                    </span>
                    <span class="text-emerald-700 font-bold">✓ Jawaban lama tetap tayang di halaman mahasiswa</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert Pembaruan Kebijakan: Menunggu Validasi -->
    @if($pertanyaan->is_pembaruan_menunggu_validasi)
        <div class="bg-purple-50 border-2 border-purple-300 text-purple-950 p-5 rounded-3xl text-xs shadow-sm flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div class="space-y-1.5 flex-1">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="font-extrabold text-sm text-purple-950">Pembaruan Jawaban Telah Diajukan & Sedang Menunggu Validasi</span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-purple-200 text-purple-900">Pembaruan Ditinjau</span>
                </div>
                <p class="text-xs text-purple-900 font-medium leading-relaxed">
                    Data Entry telah mengirimkan draf jawaban baru sesuai instruksi kebijakan. Selama peninjauan validator, jawaban lama tetap tayang di halaman mahasiswa.
                </p>
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

            <!-- Jawaban Terkini -->
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">JAWABAN TERVERIFIKASI / TERKINI</span>
                <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-100 text-gray-800 text-xs sm:text-sm font-medium leading-relaxed whitespace-pre-line">
                    {{ $pertanyaan->isi_jawaban }}
                </div>
            </div>

            @if($pertanyaan->is_pembaruan_menunggu_validasi && $pertanyaan->draft_jawaban)
                <!-- Jawaban Baru yang Diajukan (Sedang Menunggu Validasi) -->
                <div>
                    <span class="text-[11px] font-bold text-purple-700 uppercase tracking-wider block mb-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        JAWABAN BARU YANG DIAJUKAN (DRAFT PEMBARUAN KEBIJAKAN)
                    </span>
                    <div class="bg-purple-50/60 p-5 rounded-2xl border border-purple-200 text-purple-950 text-xs sm:text-sm font-medium leading-relaxed whitespace-pre-line">
                        {{ $pertanyaan->draft_jawaban }}
                    </div>
                </div>
            @endif

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

            <hr class="border-gray-100">

            <!-- Riwayat Perubahan & Revisi (Edit History) -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">RIWAYAT PERUBAHAN & REVISI</span>
                        @if($pertanyaan->riwayatJawaban && $pertanyaan->riwayatJawaban->count() > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#FFF5F6] text-[#741228] border border-rose-200">
                                {{ $pertanyaan->riwayatJawaban->count() }} Perubahan
                            </span>
                        @endif
                    </div>
                </div>

                @if($pertanyaan->riwayatJawaban && $pertanyaan->riwayatJawaban->count() > 0)
                    <div class="space-y-4">
                        @foreach($pertanyaan->riwayatJawaban as $index => $riwayat)
                            <div class="p-4 sm:p-5 rounded-2xl border border-gray-100 bg-gray-50/60 shadow-2xs space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-gray-200/60">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-lg font-bold text-[11px] bg-[#741228] text-white">
                                            Revisi #{{ $pertanyaan->riwayatJawaban->count() - $index }}
                                        </span>
                                        <span class="text-xs text-gray-600 font-medium">
                                            Diedit oleh <strong class="text-gray-900">{{ $riwayat->user->name ?? 'User' }}</strong>
                                        </span>
                                    </div>
                                    <span class="text-gray-400 text-xs font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $riwayat->created_at ? $riwayat->created_at->isoFormat('D MMMM Y, HH:mm') : '-' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                                    <!-- Jawaban Sebelumnya -->
                                    <div class="p-3.5 bg-white rounded-xl border border-rose-100/90 shadow-2xs space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                                Jawaban Sebelumnya
                                            </span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100">Sebelum</span>
                                        </div>
                                        <div class="text-xs text-gray-600 font-normal leading-relaxed whitespace-pre-line bg-rose-50/30 p-2.5 rounded-lg border border-rose-100/50">
                                            {{ $riwayat->jawaban_lama ?? '(Tidak ada data jawaban sebelumnya)' }}
                                        </div>
                                    </div>

                                    <!-- Jawaban Baru -->
                                    <div class="p-3.5 bg-white rounded-xl border border-emerald-100/90 shadow-2xs space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Jawaban Baru
                                            </span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Baru</span>
                                        </div>
                                        <div class="text-xs text-gray-800 font-medium leading-relaxed whitespace-pre-line bg-emerald-50/30 p-2.5 rounded-lg border border-emerald-100/50">
                                            {{ $riwayat->jawaban_baru }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-5 rounded-2xl border border-dashed border-gray-200 text-center text-gray-400 text-xs py-6 bg-gray-50/30">
                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-1.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-semibold text-gray-500">Belum ada riwayat perubahan</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Pertanyaan dan jawaban ini masih merupakan versi awal.</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Meta Sidebar Card (1 Col) -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-5">
            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">
                INFORMASI DOKUMEN
            </h3>

            <!-- Status -->
            <div class="pb-3 border-b border-gray-50">
                <span class="text-[11px] text-gray-400 font-medium block mb-1">Status Validasi</span>
                @if((int)$pertanyaan->status === 1)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Menunggu Validasi
                    </span>
                @elseif((int)$pertanyaan->status === 2)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Disetujui
                    </span>
                @elseif((int)$pertanyaan->status === 3)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Ditolak / Perlu Revisi
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        Draft
                    </span>
                @endif
            </div>

            <!-- Sasaran Fakultas -->
            <div class="pb-3 border-b border-gray-50">
                <span class="text-[11px] text-gray-400 font-medium block mb-1">Sasaran Fakultas</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-bold {{ $pertanyaan->badge_class }}">
                    {{ $pertanyaan->nama_fakultas }}
                </span>
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

            <!-- Total Riwayat Edit -->
            <div class="pb-3 border-b border-gray-50">
                <span class="text-[11px] text-gray-400 font-medium block mb-0.5">Total Riwayat Perubahan</span>
                <p class="text-xs font-bold text-gray-800">
                    {{ $pertanyaan->riwayatJawaban ? $pertanyaan->riwayatJawaban->count() : 0 }} kali diedit
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

            @if($pertanyaan->status_pembaruan)
                <!-- Status Pembaruan -->
                <div class="pt-3 border-t border-gray-50">
                    <span class="text-[11px] text-gray-400 font-medium block mb-1">Status Pembaruan</span>
                    @if($pertanyaan->is_perlu_pembaruan)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            Perlu Pembaruan
                        </span>
                    @elseif($pertanyaan->is_pembaruan_menunggu_validasi)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            Pembaruan Ditinjau
                        </span>
                    @endif
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
            <form action="{{ route('pertanyaan.mintaPembaruan', $pertanyaan->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="catatan_pembaruan" class="block text-xs font-bold text-gray-800 mb-1">
                        Catatan Arahan Kebijakan Baru <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-gray-400 mb-2">Tuliskan instruksi atau poin kebijakan baru yang harus disesuaikan oleh Data Entry.</p>
                    <textarea id="catatan_pembaruan" name="catatan_pembaruan" rows="4" required placeholder="Contoh: Perbarui jawaban sesuai SK Rektor No. 12/2026 mengenai perpanjangan masa pembayaran SPP..." class="w-full p-3.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:bg-white focus:outline-none focus:border-[#741228] transition leading-relaxed"></textarea>
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

</div>
@endsection

@section('scripts')
<script>
    function openModalMintaPembaruan() {
        const m = document.getElementById('modalMintaPembaruan');
        if (m) m.classList.remove('hidden');
    }

    function closeModalMintaPembaruan() {
        const m = document.getElementById('modalMintaPembaruan');
        if (m) m.classList.add('hidden');
    }
</script>
@endsection

@extends('layouts.validator')

@section('title', 'Review Pertanyaan - ASK UBAYA')
@section('page_title', 'Review Pertanyaan')

@php
    $categoryName = $pertanyaan->kategori->nama_kategori ?? 'Umum';
    $authorName = $pertanyaan->user->name ?? 'Kontributor';
    $timeString = '-';
    if ($pertanyaan->created_at) {
        if ($pertanyaan->created_at->isToday()) {
            $timeString = 'Hari ini, ' . $pertanyaan->created_at->format('H:i');
        } elseif ($pertanyaan->created_at->isYesterday()) {
            $timeString = 'Kemarin, ' . $pertanyaan->created_at->format('H:i');
        } else {
            $timeString = $pertanyaan->created_at->format('d M Y, H:i');
        }
    }
    $statusLabel = (int)$pertanyaan->status === \App\Models\Pertanyaan::MENUNGGU_VALIDASI ? 'Menunggu Validasi' : ((int)$pertanyaan->status === \App\Models\Pertanyaan::DISETUJUI ? 'Disetujui' : ((int)$pertanyaan->status === \App\Models\Pertanyaan::DITOLAK ? 'Perlu Revisi' : 'Draft'));

    $isUmum = !$pertanyaan->fakultas || $pertanyaan->fakultas === 'umum';
    $fakultasKode = $pertanyaan->fakultas ?? 'umum';
    $fakultasNama = $pertanyaan->nama_fakultas ?? 'Umum (Semua Mahasiswa)';

    // Warna info-box berdasarkan cakupan
    if ($isUmum) {
        $boxBg    = 'bg-blue-50 border-blue-200';
        $iconBg   = 'bg-blue-100 text-blue-600';
        $titleCol = 'text-blue-800';
        $descCol  = 'text-blue-600';
        $icon     = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        $desc     = 'Jawaban ini berlaku untuk seluruh mahasiswa UBAYA dari semua fakultas.';
    } else {
        $boxBg    = 'bg-amber-50 border-amber-200';
        $iconBg   = 'bg-amber-100 text-amber-600';
        $titleCol = 'text-amber-800';
        $descCol  = 'text-amber-700';
        $icon     = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>';
        $desc     = 'Jawaban ini bersifat khusus dan hanya relevan untuk mahasiswa ' . $fakultasNama . '. Pastikan isinya sesuai kebijakan fakultas tersebut.';
    }
@endphp

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-5">
            <a href="{{ route('validator.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#741228] hover:text-[#5e0d1f] transition">
                &larr; Kembali ke Daftar Validasi
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <!-- Category & Status Badge -->
            <div class="flex items-center justify-between gap-3 mb-2 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#741228]">
                        {{ strtoupper($categoryName) }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full border {{ $pertanyaan->badge_class }}">
                        <span>Sasaran: {{ $pertanyaan->nama_fakultas }}</span>
                    </span>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ (int)$pertanyaan->status === \App\Models\Pertanyaan::MENUNGGU_VALIDASI ? 'text-amber-700 bg-amber-50 border-amber-200/60' : ((int)$pertanyaan->status === \App\Models\Pertanyaan::DISETUJUI ? 'text-emerald-700 bg-emerald-50 border-emerald-200/60' : 'text-rose-700 bg-rose-50 border-rose-200/60') }} px-2.5 py-1 rounded-full border">
                    <span class="w-1.5 h-1.5 rounded-full {{ (int)$pertanyaan->status === \App\Models\Pertanyaan::MENUNGGU_VALIDASI ? 'bg-amber-500' : ((int)$pertanyaan->status === \App\Models\Pertanyaan::DISETUJUI ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                    {{ $statusLabel }}
                </span>
            </div>

            <!-- Big Title -->
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug mb-2">
                {{ $pertanyaan->judul_pertanyaan }}
            </h2>

            <!-- Sub Metadata -->
            <p class="text-xs text-gray-400 mb-4">
                Diajukan oleh <span class="text-gray-700 font-medium">{{ $authorName }}</span> &bull; {{ $timeString }}
            </p>

            <!-- Info Box: Cakupan Sasaran Fakultas -->
            <div class="mb-6 flex items-start gap-3 p-4 rounded-xl border {{ $boxBg }}">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $iconBg }}">
                    {!! $icon !!}
                </div>
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider {{ $titleCol }} mb-0.5">
                        Cakupan Sasaran
                    </p>
                    <p class="text-sm font-bold {{ $titleCol }}">
                        {{ $isUmum ? '🌐 Umum — Semua Mahasiswa UBAYA' : '🏛️ Khusus ' . $fakultasNama }}
                    </p>
                    <p class="text-xs mt-1 {{ $descCol }}">
                        {{ $desc }}
                    </p>
                </div>
            </div>

            @php
                $riwayatTerbaru = $pertanyaan->riwayatJawaban->first();
                $hasRevisi = $riwayatTerbaru && !empty($riwayatTerbaru->jawaban_lama);
                $isPembaruan = $pertanyaan->status_pembaruan === \App\Models\Pertanyaan::STATUS_PEMBARUAN_MENUNGGU;
            @endphp

            @if($isPembaruan)
                <!-- Banner Pembaruan Kebijakan Baru -->
                <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-2xl text-xs space-y-2 text-purple-950 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold flex items-center gap-1.5 text-purple-900 text-sm">
                            <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pengajuan Pembaruan Jawaban (Kebijakan Baru)
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-200 text-purple-900">
                            Menunggu Validasi
                        </span>
                    </div>
                    <p class="text-xs text-purple-900 bg-white/70 p-2.5 rounded-xl border border-purple-100">
                        Arahan perubahan: <strong>{{ $pertanyaan->catatan_pembaruan }}</strong>
                    </p>
                    <div class="text-[11px] text-purple-700 flex items-center justify-between flex-wrap gap-2 pt-0.5">
                        <span>Diminta oleh: <strong>{{ $pertanyaan->pembaruanDimintaOlehUser->name ?? 'Validator / Super Admin' }}</strong></span>
                        <span class="text-emerald-700 font-bold">✓ Jawaban lama masih tayang di mahasiswa</span>
                    </div>
                </div>
            @endif

            <!-- Section: Jawaban & Perbandingan Revisi -->
            <div class="mb-6 space-y-4">
                @if($isPembaruan)
                    <!-- Box Jawaban Lama Saat Ini -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                JAWABAN LAMA (SEDANG TAYANG DI MAHASISWA)
                            </h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Sedang Tayang
                            </span>
                        </div>
                        <div class="text-sm leading-relaxed text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-200 whitespace-pre-line">
                            {{ $pertanyaan->isi_jawaban }}
                        </div>
                    </div>

                    <!-- Box Jawaban Baru yang Diajukan -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold text-purple-700 uppercase tracking-wider flex items-center gap-1.5">
                                <span>JAWABAN BARU (DIAJUKAN SESUAI KEBIJAKAN BARU)</span>
                            </h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                Diajukan Baru
                            </span>
                        </div>
                        <div class="text-sm leading-relaxed text-gray-800 bg-purple-50/20 border-purple-200/70 p-4 rounded-xl border whitespace-pre-line">
                            {{ $pertanyaan->draft_jawaban }}
                        </div>
                    </div>
                @else
                    @if($hasRevisi)
                        <!-- Info Banner Jika Ada Revisi -->
                        <div class="p-3.5 bg-amber-50/80 border border-amber-200 rounded-xl text-xs flex items-start gap-2.5 text-amber-900">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <span class="font-bold">Pertanyaan ini telah direvisi:</span>
                                <span class="text-amber-800 ml-1">
                                    Diedit oleh <strong>{{ $riwayatTerbaru->user->name ?? 'Kontributor' }}</strong> ({{ $riwayatTerbaru->created_at ? $riwayatTerbaru->created_at->isoFormat('D MMMM Y, HH:mm') : '-' }})
                                </span>
                            </div>
                        </div>

                        <!-- Box Jawaban Sebelumnya -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold text-rose-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                    JAWABAN SEBELUMNYA (VERSI LAMA)
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                    Sebelum Revisi
                                </span>
                            </div>
                            <div class="text-sm leading-relaxed text-gray-600 bg-rose-50/20 p-4 rounded-xl border border-rose-200/70 whitespace-pre-line">
                                {{ $riwayatTerbaru->jawaban_lama }}
                            </div>
                        </div>
                    @endif

                    <!-- Box Jawaban Baru / Yang Diajukan -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold {{ $hasRevisi ? 'text-emerald-700' : 'text-gray-400' }} uppercase tracking-wider flex items-center gap-1.5">
                                <span>{{ $hasRevisi ? 'JAWABAN BARU (HASIL REVISI)' : 'JAWABAN YANG DIAJUKAN' }}</span>
                            </h4>
                            @if($hasRevisi)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Versi Terbaru
                                </span>
                            @endif
                        </div>
                        <div class="text-sm leading-relaxed text-gray-800 {{ $hasRevisi ? 'bg-emerald-50/20 border-emerald-200/70' : 'bg-gray-50/50 border-gray-100' }} p-4 rounded-xl border whitespace-pre-line">
                            {{ $pertanyaan->isi_jawaban }}
                        </div>
                    </div>
                @endif

                @if($pertanyaan->riwayatJawaban->count() > 1)
                    <!-- Riwayat Revisi Lengkap (Accordion) -->
                    <details class="group border border-gray-200 rounded-xl overflow-hidden text-xs">
                        <summary class="flex items-center justify-between p-3 bg-gray-50/70 cursor-pointer select-none font-bold text-gray-700 hover:bg-gray-100 transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Lihat Seluruh Riwayat Revisi ({{ $pertanyaan->riwayatJawaban->count() }} kali diedit)
                            </span>
                            <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 space-y-3 bg-white divide-y divide-gray-100">
                            @foreach($pertanyaan->riwayatJawaban as $idx => $r)
                                <div class="pt-3 first:pt-0 space-y-1.5">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-gray-800">Revisi #{{ $pertanyaan->riwayatJawaban->count() - $idx }} oleh {{ $r->user->name ?? 'User' }}</span>
                                        <span class="text-gray-400">{{ $r->created_at ? $r->created_at->isoFormat('D MMMM Y, HH:mm') : '-' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-lg whitespace-pre-line">
                                        <span class="font-semibold text-gray-500 block text-[10px] mb-0.5">Jawaban Baru:</span>
                                        {{ $r->jawaban_baru }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>

            @if((int)$pertanyaan->status === 2 && !$isPembaruan)
                <!-- Box Minta Pembaruan Kebijakan -->
                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Ada Perubahan Kebijakan Baru?</h4>
                        <p class="text-xs text-gray-400">Instruksikan Data Entry untuk memperbarui jawaban FAQ yang sedang tayang ini.</p>
                    </div>
                    <button type="button" onclick="openModalMintaPembaruan()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition cursor-pointer shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Minta Pembaruan</span>
                    </button>
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
            @else
                <!-- Form Keputusan Validator -->
                <form id="formReview" action="{{ route('validator.updateStatus', $pertanyaan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="status" id="inputStatus" value="2">

                    <!-- Catatan untuk Kontributor -->
                    <div class="mb-6">
                        <label for="catatan_validator" class="block text-sm font-bold text-gray-800 mb-2">
                            Catatan untuk kontributor
                        </label>
                        <textarea name="catatan_validator" id="detailCatatan" rows="4"
                                  placeholder="Tambahkan catatan jika konten perlu direvisi..."
                                  class="w-full p-3.5 bg-gray-50/60 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition resize-y">{{ $pertanyaan->catatan_validator }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" onclick="submitDecision(3)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-[#FFF1F2] text-[#9F1239] hover:bg-[#FFE4E6] border border-[#FECDD3] transition cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4 text-[#9F1239]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>{{ $isPembaruan ? 'Minta Perbaikan Ulang' : 'Minta Revisi' }}</span>
                        </button>

                        <button type="button" onclick="submitDecision(2)"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm bg-[#741228] hover:bg-[#5e0d1f] text-white transition cursor-pointer shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $isPembaruan ? 'Setujui & Publikasikan Jawaban Baru' : 'Setujui & Terbitkan' }}</span>
                        </button>
                    </div>
                </form>
            @endif
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

    function submitDecision(status) {
        const inputStatus = document.getElementById('inputStatus');
        if (inputStatus) inputStatus.value = status;
        if (status === 'ditolak' || status === 3 || status === '3') {
            const catatan = document.getElementById('detailCatatan')?.value.trim();
            if (!catatan) {
                if (!confirm('Anda belum memberikan catatan revisi untuk kontributor. Apakah Anda yakin ingin meminta revisi tanpa catatan?')) {
                    document.getElementById('detailCatatan')?.focus();
                    return;
                }
            }
        }
        document.getElementById('formReview')?.submit();
    }
</script>
@endsection

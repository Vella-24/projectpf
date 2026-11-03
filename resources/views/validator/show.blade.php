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
    $statusLabel = $pertanyaan->status === 'menunggu_validasi' ? 'Menunggu' : ($pertanyaan->status === 'disetujui' ? 'Disetujui' : ($pertanyaan->status === 'ditolak' ? 'Perlu Revisi' : ucwords(str_replace('_', ' ', $pertanyaan->status))));
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
            <div class="flex items-center justify-between gap-3 mb-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#741228]">
                    {{ strtoupper($categoryName) }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $pertanyaan->status === 'menunggu_validasi' ? 'text-amber-700 bg-amber-50 border-amber-200/60' : ($pertanyaan->status === 'disetujui' ? 'text-emerald-700 bg-emerald-50 border-emerald-200/60' : 'text-rose-700 bg-rose-50 border-rose-200/60') }} px-2.5 py-1 rounded-full border">
                    <span class="w-1.5 h-1.5 rounded-full {{ $pertanyaan->status === 'menunggu_validasi' ? 'bg-amber-500' : ($pertanyaan->status === 'disetujui' ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                    {{ $statusLabel }}
                </span>
            </div>

            <!-- Big Title -->
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug mb-2">
                {{ $pertanyaan->judul_pertanyaan }}
            </h2>

            <!-- Sub Metadata -->
            <p class="text-xs text-gray-400 mb-6">
                Diajukan oleh <span class="text-gray-700 font-medium">{{ $authorName }}</span> &bull; {{ $timeString }}
            </p>

            <!-- Jawaban Yang Diajukan -->
            <div class="mb-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2.5">
                    JAWABAN YANG DIAJUKAN
                </h4>
                <div class="text-sm leading-relaxed text-gray-700 bg-gray-50/50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
                    {{ $pertanyaan->isi_jawaban }}
                </div>
            </div>

            <!-- Form Keputusan Validator -->
            <form id="formReview" action="{{ route('validator.updateStatus', $pertanyaan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="status" id="inputStatus" value="disetujui">

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
                    <button type="button" onclick="submitDecision('ditolak')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-[#FFF1F2] text-[#9F1239] hover:bg-[#FFE4E6] border border-[#FECDD3] transition cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4 text-[#9F1239]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Minta Revisi</span>
                    </button>

                    <button type="button" onclick="submitDecision('disetujui')"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm bg-[#741228] hover:bg-[#5e0d1f] text-white transition cursor-pointer shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Setujui & Terbitkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function submitDecision(status) {
        document.getElementById('inputStatus').value = status;
        if (status === 'ditolak') {
            const catatan = document.getElementById('detailCatatan').value.trim();
            if (!catatan) {
                if (!confirm('Anda belum memberikan catatan revisi untuk kontributor. Apakah Anda yakin ingin meminta revisi tanpa catatan?')) {
                    document.getElementById('detailCatatan').focus();
                    return;
                }
            }
        }
        document.getElementById('formReview').submit();
    }
</script>
@endsection

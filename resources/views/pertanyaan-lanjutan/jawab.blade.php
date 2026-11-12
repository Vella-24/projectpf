
@extends('layouts.data-entry')

@section('title', 'ASK UBAYA - Jawab Pertanyaan Mahasiswa')
@section('page_title', 'Jawab Pertanyaan Mahasiswa')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div>
        <a href="{{ route('pertanyaanLanjutan.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-[#741228] hover:underline mb-4">
            ← Kembali ke Pertanyaan Mahasiswa
        </a>

        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">
            Jawab Pertanyaan Mahasiswa
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Siapkan jawaban resmi untuk diperiksa oleh Validator.
        </p>
    </div>

    @if (session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-rose-50 text-rose-700 border border-rose-200 p-4 rounded-xl text-sm">
            <p class="font-bold mb-2">Periksa kembali formulir:</p>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- INFORMASI PERTANYAAN MAHASISWA -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <div class="flex items-center gap-3 mb-5">
            <span class="w-9 h-9 rounded-full bg-[#741228] text-white flex items-center justify-center font-bold">
                1
            </span>
            <h3 class="font-extrabold text-gray-900 text-lg">
                Pertanyaan Mahasiswa
            </h3>
        </div>

        <div class="bg-[#FFF7F8] border border-rose-100 rounded-xl p-5">

            <p class="text-xs font-bold text-[#741228] uppercase mb-2">
                Pertanyaan yang perlu dijawab
            </p>

            <p class="text-base font-bold text-gray-900">
                {{ $tugas->isi_pertanyaan }}
            </p>

            <div class="mt-4 pt-4 border-t border-rose-100 text-xs text-gray-500 space-y-1">
                <p>
                    <strong>Mahasiswa:</strong>
                    {{ $tugas->nama_mahasiswa }}
                </p>
                <p>
                    <strong>Email:</strong>
                    {{ $tugas->email_mahasiswa }}
                </p>
                <p>
                    <strong>FAQ asal:</strong>
                    {{ $tugas->pertanyaan->judul_pertanyaan ?? '-' }}
                </p>
                <p>
                    <strong>Kategori:</strong>
                    {{ $tugas->pertanyaan->kategori->nama_kategori ?? '-' }}
                </p>
            </div>

            {{-- Jawaban FAQ Asal --}}
            @if($tugas->pertanyaan?->isi_jawaban)
                <div class="mt-4 pt-4 border-t border-rose-100">
                    <p class="text-xs font-bold text-[#741228] uppercase mb-2">Jawaban FAQ Asal</p>
                    <div class="bg-white rounded-lg p-3 text-sm text-gray-700 leading-relaxed whitespace-pre-line border border-rose-50">
                        {{ $tugas->pertanyaan->isi_jawaban }}
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- FORM JAWABAN -->
    <form id="formJawaban"
          action="{{ route('pertanyaanLanjutan.simpanJawaban', $tugas->id) }}"
          method="POST"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">

        @csrf

        <div class="flex items-center gap-3 mb-6">
            <span class="w-9 h-9 rounded-full bg-[#741228] text-white flex items-center justify-center font-bold">
                2
            </span>

            <h3 class="font-extrabold text-gray-900 text-lg">
                Jawaban
            </h3>
        </div>

        <!-- JUDUL FAQ -->
        <div class="mb-6">
            <label for="judul_pertanyaan"
                   class="block text-sm font-bold text-gray-900 mb-2">
                Judul Pertanyaan FAQ
                <span class="text-red-600">*</span>
            </label>

            <input
                type="text"
                id="judul_pertanyaan"
                name="judul_pertanyaan"
                required
                maxlength="1000"
                value="{{ old('judul_pertanyaan', $tugas->faqBaru->judul_pertanyaan ?? $tugas->isi_pertanyaan) }}"
                class="w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] text-sm"
            >

            <p class="text-xs text-gray-400 mt-2">
                Judul FAQ diisi dari pertanyaan mahasiswa dan dapat diperjelas sebelum dikirim.
            </p>
        </div>

        <!-- ISI JAWABAN -->
        <div class="mb-7">
            <label for="isi_jawaban"
                   class="block text-sm font-bold text-gray-900 mb-2">
                Jawaban yang Diajukan
                <span class="text-red-600">*</span>
            </label>

            <textarea
                id="isi_jawaban"
                name="isi_jawaban"
                rows="8"
                required
                placeholder="Tuliskan jawaban yang jelas, ringkas, dan mudah dipahami mahasiswa..."
                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] text-sm resize-y"
            >{{ old('isi_jawaban', $tugas->faqBaru->isi_jawaban ?? '') }}</textarea>
        </div>

        <!-- KATA KUNCI -->
        <div class="mb-9">

            <label for="input-tag"
                   class="block text-sm font-bold text-gray-900 mb-2">
                Kata Kunci
            </label>

            <div class="flex gap-2">
                <input
                    type="text"
                    id="input-tag"
                    list="saran-tag"
                    placeholder="Ketik kata kunci lalu tekan Enter"
                    class="flex-1 min-w-0 px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] text-sm"
                >

                <button type="button"
                        id="tambah-tag"
                        class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold">
                    Tambah
                </button>
            </div>

            <datalist id="saran-tag">
                @foreach ($tags as $tag)
                    <option value="{{ $tag->nama_tag }}"></option>
                @endforeach
            </datalist>

            <div id="daftar-tag"
                 class="flex flex-wrap gap-2 mt-3">

                @foreach (
                    old('tags', $tugas->faqBaru ? $tugas->faqBaru->tags->pluck('nama_tag')->toArray() : [])
                    as $namaTag
                )
                    <span class="inline-flex items-center gap-2 bg-rose-50 text-[#741228] px-3 py-1.5 rounded-lg text-xs font-semibold border border-rose-100">
                        <span>{{ $namaTag }}</span>
                        <button type="button"
                                class="hapus-tag"
                                aria-label="Hapus kata kunci">
                            ×
                        </button>
                        <input type="hidden" name="tags[]" value="{{ $namaTag }}">
                    </span>
                @endforeach

            </div>

            <p class="text-xs text-gray-400 mt-2">
                Kata kunci membantu mahasiswa menemukan jawaban ini.
            </p>
        </div>

        <!-- INPUT AKSI -->
        <input type="hidden" name="aksi" id="aksiJawaban" value="draft">

        <!-- TOMBOL AKSI -->
        <div class="flex flex-wrap items-center justify-end gap-3 pt-6 border-t border-gray-100">

            <button type="submit"
                    onclick="document.getElementById('aksiJawaban').value='draft'"
                    class="px-6 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-800 hover:bg-gray-50">
                Simpan Draft
            </button>

            <button type="submit"
                    onclick="document.getElementById('aksiJawaban').value='kirim'"
                    class="px-6 py-3 rounded-xl bg-[#741228] hover:bg-[#5e0d1f] text-white text-sm font-bold shadow-sm">
                Kirim untuk Validasi →
            </button>

        </div>

    </form>

    {{-- Form Tutup — TERPISAH dari form utama agar tidak perlu isi jawaban --}}
    <form action="{{ route('pertanyaanLanjutan.tutup', $tugas->id) }}" method="POST"
          onsubmit="return confirm('Yakin menutup pertanyaan ini? Jawaban FAQ asal dianggap sudah cukup jelas.')">
        @csrf
        @method('PATCH')
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm font-bold text-amber-800">Jawaban FAQ asal sudah cukup jelas?</p>
                <p class="text-xs text-amber-700 mt-0.5">Klik tombol ini untuk menutup pertanyaan tanpa perlu menulis jawaban baru.</p>
            </div>
            <button type="submit"
                    class="shrink-0 px-6 py-3 rounded-xl border border-amber-400 bg-white text-sm font-bold text-amber-800 hover:bg-amber-100 transition">
                Tutup Pertanyaan Ini
            </button>
        </div>
    </form>

</div>

@endsection

@section('scripts')
<script>
    const inputTag = document.getElementById('input-tag');
    const tombolTambah = document.getElementById('tambah-tag');
    const daftarTag = document.getElementById('daftar-tag');

    function tambahTag() {
        const nama = inputTag.value.trim().toLowerCase();

        if (!nama) return;

        const sudahAda = Array.from(
            daftarTag.querySelectorAll('input[name="tags[]"]')
        ).some(input => input.value.toLowerCase() === nama);

        if (sudahAda) {
            inputTag.value = '';
            return;
        }

        const chip = document.createElement('span');
        chip.className =
            'inline-flex items-center gap-2 bg-rose-50 text-[#741228] ' +
            'px-3 py-1.5 rounded-lg text-xs font-semibold border border-rose-100';

        const label = document.createElement('span');
        label.textContent = nama;

        const tombolHapus = document.createElement('button');
        tombolHapus.type = 'button';
        tombolHapus.className = 'hapus-tag';
        tombolHapus.textContent = '×';

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'tags[]';
        hiddenInput.value = nama;

        chip.appendChild(label);
        chip.appendChild(tombolHapus);
        chip.appendChild(hiddenInput);

        daftarTag.appendChild(chip);

        inputTag.value = '';
    }

    tombolTambah.addEventListener('click', tambahTag);

    inputTag.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            tambahTag();
        }
    });

    daftarTag.addEventListener('click', function(event) {
        if (event.target.classList.contains('hapus-tag')) {
            event.target.closest('span.inline-flex').remove();
        }
    });
</script>
@endsection

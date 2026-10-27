@extends('layouts.data-entry')

@section('title', 'ASK UBAYA - Ubah Pengetahuan')
@section('page_title', 'Ubah Pengetahuan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Form Pembungkus Utama -->
    <form id="form-pengetahuan" action="{{ route('pertanyaan.update', $pertanyaan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Input Tersembunyi untuk Status -->
        <input type="hidden" name="status" id="input-status" value="{{ old('status', $pertanyaan->status) }}">

        <!-- Sub-Bar Navigasi & Aksi Tombol -->
        <div class="flex items-center justify-between pb-2">
            <!-- Tombol Kembali -->
            <a href="{{ route('pertanyaan.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </a>

            <!-- Tombol Aksi Simpan Draf & Kirim Validasi -->
            <div class="flex items-center gap-3">
                <!-- Simpan sebagai Draf -->
                <button type="button" 
                        onclick="submitDenganStatus('draft')" 
                        class="px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-2xl text-xs font-bold transition shadow-sm hover:shadow">
                    Simpan Draf
                </button>

                <!-- Kirim Ulang untuk Validasi -->
                <button type="button" 
                        onclick="submitDenganStatus('menunggu_validasi')" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-md hover:shadow-lg">
                    <span>Kirim untuk Validasi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Judul Halaman & Subjudul -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Ubah pengetahuan
                </h1>
                @if($pertanyaan->status === 'menunggu_validasi')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Menunggu Validasi
                    </span>
                @elseif($pertanyaan->status === 'disetujui')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Telah Diterbitkan
                    </span>
                @elseif($pertanyaan->status === 'ditolak')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Perlu Direvisi
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        Draf
                    </span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
                Perbarui pertanyaan, jawaban, atau perbaiki catatan dari validator.
            </p>
        </div>

        <!-- Alert Catatan Validator jika statusnya ditolak / revisi -->
        @if($pertanyaan->catatan_validator)
            <div class="bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-2xl mb-6 text-xs shadow-sm flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-amber-900">Catatan Perbaikan dari Validator:</p>
                    <p class="mt-0.5 text-amber-800 font-medium leading-relaxed">{{ $pertanyaan->catatan_validator }}</p>
                </div>
            </div>
        @endif

        <!-- Alert Error Validasi jika ada -->
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl mb-6 text-xs shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                    </svg>
                    <span>Mohon lengkapi formulir dengan benar:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-6 text-rose-700">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Utama (Single Card Tanpa Status Dokumen Duplikat) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-gray-100 shadow-sm space-y-8 max-w-4xl">
            
            <!-- BAGIAN 1: Informasi Pertanyaan -->
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-[#741228] text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                        1
                    </div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Informasi pertanyaan</h2>
                </div>

                <!-- Pertanyaan * -->
                <div>
                    <label for="judul_pertanyaan" class="block text-xs font-bold text-gray-800 mb-1">
                        Pertanyaan <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-gray-400 mb-2">
                        Tuliskan pertanyaan dengan bahasa yang biasa digunakan mahasiswa.
                    </p>
                    <input type="text" 
                           id="judul_pertanyaan" 
                           name="judul_pertanyaan" 
                           value="{{ old('judul_pertanyaan', $pertanyaan->judul_pertanyaan) }}" 
                           placeholder="Contoh: Bagaimana cara mengajukan cuti kuliah?" 
                           required 
                           class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-sm">
                </div>

                <!-- Kategori Pertanyaan * (Data Entry hanya memilih dari kategori yang ada) -->
                <div>
                    <label for="kategori_id" class="block text-xs font-bold text-gray-800 mb-1">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select id="kategori_id" 
                            name="kategori_id" 
                            required 
                            class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-sm">
                        <option value="">Pilih kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id', $pertanyaan->kategori_id) == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- BAGIAN 2: Jawaban -->
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-[#741228] text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                        2
                    </div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Jawaban</h2>
                </div>

                <!-- Jawaban Terverifikasi * -->
                <div>
                    <label for="isi_jawaban" class="block text-xs font-bold text-gray-800 mb-1">
                        Jawaban terverifikasi <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="isi_jawaban" 
                              name="isi_jawaban" 
                              rows="6" 
                              placeholder="Tuliskan jawaban yang jelas, ringkas, dan mudah dipahami mahasiswa..." 
                              required 
                              class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-sm resize-y leading-relaxed">{{ old('isi_jawaban', $pertanyaan->isi_jawaban) }}</textarea>
                </div>

                <!-- Kata Kunci (Tag System Asli yang dipertahankan) -->
                <div>
                    <label for="input-tag" class="block text-xs font-bold text-gray-800 mb-1">
                        Kata kunci
                    </label>

                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="text" 
                               id="input-tag" 
                               list="saran-tag" 
                               placeholder="Ketik kata kunci lalu tekan Enter" 
                               autocomplete="off" 
                               class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-sm">
                        
                        <datalist id="saran-tag">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->nama_tag }}"></option>
                            @endforeach
                        </datalist>

                        <button type="button" 
                                id="tambah-tag" 
                                class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl text-xs font-bold transition shrink-0">
                            Tambah
                        </button>
                    </div>
                    
                    <p class="text-[11px] text-gray-400 mb-3">
                        Kata kunci membantu mahasiswa menemukan jawaban ini.
                    </p>

                    <!-- Chip Tag Container -->
                    <div id="daftar-tag" class="flex flex-wrap gap-2 min-h-[32px]">
                        @foreach (old('tags', $pertanyaan->tags->pluck('nama_tag')->toArray()) as $namaTag)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-rose-50 text-[#741228] border border-rose-200/80 shadow-2xs">
                                <span>{{ $namaTag }}</span>
                                <button type="button" class="hapus-tag text-rose-400 hover:text-rose-700 text-sm leading-none">&times;</button>
                                <input type="hidden" name="tags[]" value="{{ $namaTag }}">
                            </span>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Tombol Aksi di Bagian Bawah Formulir -->
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                <a href="{{ route('pertanyaan.index') }}" 
                   class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-2xl text-xs font-bold transition text-center border border-gray-200">
                    Batal
                </a>
                <button type="button" 
                        onclick="submitDenganStatus('draft')" 
                        class="px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-2xl text-xs font-bold transition shadow-sm hover:shadow text-center">
                    Simpan Draf
                </button>
                <button type="button" 
                        onclick="submitDenganStatus('menunggu_validasi')" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-md hover:shadow-lg text-center">
                    <span>Kirim untuk Validasi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>

        </div>

    </form>

</div>
@endsection

@section('scripts')
<script>
    // Penanganan tombol Simpan Draf vs Kirim untuk Validasi
    function submitDenganStatus(statusBaru) {
        document.getElementById('input-status').value = statusBaru;
        document.getElementById('form-pengetahuan').submit();
    }

    // Penanganan Kata Kunci / Tag Asli
    const inputTag = document.getElementById('input-tag');
    const daftarTag = document.getElementById('daftar-tag');
    const tombolTambah = document.getElementById('tambah-tag');

    function tambahTag() {
        const namaTag = inputTag.value.trim().toLowerCase();

        if (namaTag === '') return;

        const tagSudahAda = Array.from(
            daftarTag.querySelectorAll('input[name="tags[]"]')
        ).some(function(input) {
            return input.value.toLowerCase() === namaTag;
        });

        if (tagSudahAda) {
            inputTag.value = '';
            return;
        }

        const item = document.createElement('span');
        item.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-rose-50 text-[#741228] border border-rose-200/80 shadow-2xs';

        const teks = document.createElement('span');
        teks.textContent = namaTag;

        const tombolHapus = document.createElement('button');
        tombolHapus.type = 'button';
        tombolHapus.className = 'hapus-tag text-rose-400 hover:text-rose-700 text-sm leading-none';
        tombolHapus.innerHTML = '&times;';

        const inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = 'tags[]';
        inputHidden.value = namaTag;

        tombolHapus.addEventListener('click', function() {
            item.remove();
        });

        item.appendChild(teks);
        item.appendChild(tombolHapus);
        item.appendChild(inputHidden);

        daftarTag.appendChild(item);
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
            event.target.parentElement.remove();
        }
    });
</script>
@endsection

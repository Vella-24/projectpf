@extends('layouts.data-entry')

@section('title', 'ASK UBAYA - Buat Pengetahuan Baru')
@section('page_title', 'Buat Pengetahuan Baru')

@section('content')
<div class="space-y-6 w-full">

    <!-- Form Pembungkus Utama -->
    <form id="form-pengetahuan" action="{{ route('pertanyaan.store') }}" method="POST">
        @csrf

        <!-- Input Tersembunyi untuk Status (Default: menunggu_validasi, bisa diubah oleh tombol Simpan Draf) -->
        <input type="hidden" name="status" id="input-status" value="{{ old('status', 'menunggu_validasi') }}">

        <!-- Sub-Bar Navigasi & Aksi Tombol (Gambar 4) -->
        <div class="flex items-center justify-between pb-2">
            <!-- Tombol Kembali -->
            <a href="{{ route('pertanyaan.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </a>

            <!-- Tombol Aksi Simpan Draft & Kirim Validasi -->
            <div class="flex items-center gap-3">
                <!-- Simpan Draft -->
                <button type="button" 
                        onclick="submitDenganStatus('draft')" 
                        class="btn-submit-action px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-2xl text-xs font-bold transition shadow-sm hover:shadow">
                    Simpan Draft
                </button>

                <!-- Kirim untuk Validasi -->
                <button type="button" 
                        onclick="submitDenganStatus('menunggu_validasi')" 
                        class="btn-submit-action inline-flex items-center gap-2 px-6 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-md hover:shadow-lg">
                    <span>Kirim untuk Validasi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Judul Halaman & Subjudul (Gambar 4) -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                Buat pengetahuan baru
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
                Tambahkan pasangan pertanyaan dan jawaban untuk ditinjau validator.
            </p>
        </div>

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
        <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-gray-100 shadow-sm space-y-8">
            
            <!-- BAGIAN 1: Informasi Pertanyaan (Gambar 4) -->
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
                           value="{{ old('judul_pertanyaan') }}" 
                           placeholder="Contoh: Bagaimana cara mengajukan cuti kuliah?" 
                           required 
                           autocomplete="off"
                           class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-sm">

                    <!-- Peringatan Kemiripan Fuzzy (Levenshtein Distance) - BLOKIR INPUT DUPLIKAT -->
                    <div id="kemiripan-wrapper" class="hidden mt-3">
                        <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-4 text-xs shadow-md transition-all">
                            <div class="flex items-start gap-3">
                                <span class="p-2 rounded-xl bg-rose-100 text-rose-700 shrink-0 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                                    </svg>
                                </span>
                                <div class="space-y-1">
                                    <p class="font-extrabold text-rose-900 text-sm" id="kemiripan-title">
                                        ⛔ Pertanyaan Tidak Dapat Dilanjutkan!
                                    </p>
                                    <p class="text-xs text-rose-800 leading-relaxed font-medium" id="kemiripan-desc">
                                        Sistem mendeteksi pertanyaan ini serupa dengan data yang sudah ada di database. Demi mencegah duplikasi, <strong>tombol simpan dinonaktifkan</strong> sampai Anda mengubah rumusan pertanyaan.
                                    </p>
                                </div>
                            </div>

                            <!-- Daftar Pertanyaan Mirip -->
                            <div id="daftar-pertanyaan-mirip" class="mt-3 space-y-2 border-t border-rose-200 pt-3">
                                <!-- Render dinamis via JS -->
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-rose-200 text-[11px] text-rose-700 font-medium">
                                💡 <em>Silakan gunakan pertanyaan yang sudah ada, atau ubah teks pertanyaan Anda di atas agar tidak serupa.</em>
                            </div>
                        </div>
                    </div>
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
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- BAGIAN 2: Jawaban (Gambar 5) -->
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
                              class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-sm resize-y leading-relaxed">{{ old('isi_jawaban') }}</textarea>
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
                        @foreach (old('tags', []) as $namaTag)
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
                        class="btn-submit-action px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-2xl text-xs font-bold transition shadow-sm hover:shadow text-center">
                    Simpan Draft
                </button>
                <button type="button" 
                        onclick="submitDenganStatus('menunggu_validasi')" 
                        class="btn-submit-action inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-md hover:shadow-lg text-center">
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
    // Penanganan tombol Simpan Draft vs Kirim untuk Validasi
    function submitDenganStatus(statusBaru) {
        if (typeof adaKemiripanTerdeteksi !== 'undefined' && adaKemiripanTerdeteksi) {
            alert('Pertanyaan tidak dapat disimpan karena terdeteksi serupa dengan pertanyaan yang sudah ada di database. Silakan ubah rumusan pertanyaan Anda.');
            return false;
        }
        document.getElementById('input-status').value = statusBaru;
        document.getElementById('form-pengetahuan').submit();
    }

    // Penanganan Kata Kunci / Tag Asli
    const inputTag = document.getElementById('input-tag');
    const daftarTag = document.getElementById('daftar-tag');
    const tombolTambah = document.getElementById('tambah-tag');

    function tambahTag() {
        const namaTag = inputTag.value.trim().toLowerCase();

        if (namaTag === '') {
            return;
        }

        // Cek agar tag yang sama tidak ditambahkan dua kali
        const tagSudahAda = Array.from(
            daftarTag.querySelectorAll('input[name="tags[]"]')
        ).some(function(input) {
            return input.value.toLowerCase() === namaTag;
        });

        if (tagSudahAda) {
            inputTag.value = '';
            return;
        }

        // Membuat tampilan tag chip modern
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

    // Tekan Enter untuk menambahkan tag
    inputTag.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            tambahTag();
        }
    });

    // Tombol hapus untuk tag yang sudah ada
    daftarTag.addEventListener('click', function(event) {
        if (event.target.classList.contains('hapus-tag')) {
            event.target.parentElement.remove();
        }
    });

    // =========================================================================
    // FUZZY MATCHING (LEVENSHTEIN DISTANCE) - DETEKSI PERTANYAAN SERUPA REAL-TIME
    // =========================================================================
    const inputJudul = document.getElementById('judul_pertanyaan');
    const kemiripanWrapper = document.getElementById('kemiripan-wrapper');
    const daftarMirip = document.getElementById('daftar-pertanyaan-mirip');
    const formPengetahuan = document.getElementById('form-pengetahuan');

    let timerDebounce = null;
    let adaKemiripanTerdeteksi = false;

    // Kunci atau buka tombol submit jika kemiripan terdeteksi
    function setSubmitButtonsState(isBlocked) {
        adaKemiripanTerdeteksi = isBlocked;
        const buttons = document.querySelectorAll('.btn-submit-action');
        buttons.forEach(function(btn) {
            if (isBlocked) {
                btn.disabled = true;
                btn.setAttribute('title', 'Tidak dapat menyimpan: Pertanyaan serupa terdeteksi.');
                btn.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
            } else {
                btn.disabled = false;
                btn.removeAttribute('title');
                btn.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
            }
        });
    }

    if (formPengetahuan) {
        formPengetahuan.addEventListener('submit', function(e) {
            if (adaKemiripanTerdeteksi) {
                e.preventDefault();
                alert('Pertanyaan tidak dapat disimpan karena terdeteksi serupa dengan pertanyaan yang sudah ada di database. Silakan ubah rumusan pertanyaan Anda.');
                return false;
            }
        });
    }

    inputJudul.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(timerDebounce);

        // Jika teks kurang dari 5 karakter, reset status dan sembunyikan peringatan
        if (query.length < 5) {
            kemiripanWrapper.classList.add('hidden');
            daftarMirip.innerHTML = '';
            setSubmitButtonsState(false);
            return;
        }

        // Debounce 450ms agar tidak spam request setiap huruf diketik
        timerDebounce = setTimeout(function() {
            jalankanCekKemiripan(query);
        }, 450);
    });

    async function jalankanCekKemiripan(teks) {
        try {
            const response = await fetch("{{ route('pertanyaan.cekKemiripan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    pertanyaan: teks,
                    threshold: 70 // Ambang batas kemiripan 70%
                })
            });

            if (!response.ok) return;

            const data = await response.json();

            if (data.matches && data.matches.length > 0) {
                renderPeringatanKemiripan(data.matches);
                setSubmitButtonsState(true); // BLOKIR tombol submit
            } else {
                kemiripanWrapper.classList.add('hidden');
                daftarMirip.innerHTML = '';
                setSubmitButtonsState(false); // BUKA KEMBALI tombol submit
            }
        } catch (error) {
            console.error('Gagal memeriksa kemiripan pertanyaan:', error);
        }
    }

    function renderPeringatanKemiripan(matches) {
        daftarMirip.innerHTML = '';

        matches.forEach(function(item, index) {
            const badgeWarna = item.kemiripan >= 80 
                ? 'bg-rose-100 text-rose-800 border-rose-300 font-extrabold'
                : 'bg-amber-100 text-amber-900 border-amber-300 font-bold';

            let statusWarna = 'bg-gray-100 text-gray-700';
            if (item.status === 'disetujui') {
                statusWarna = 'bg-emerald-100 text-emerald-800';
            } else if (item.status === 'menunggu_validasi') {
                statusWarna = 'bg-sky-100 text-sky-800';
            } else if (item.status === 'ditolak') {
                statusWarna = 'bg-rose-100 text-rose-700';
            }

            const card = document.createElement('div');
            card.className = 'p-3 bg-white/95 rounded-xl border border-amber-200/90 shadow-2xs space-y-1.5 transition';

            card.innerHTML = `
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] border ${badgeWarna}">
                            ${item.kemiripan}% Mirip
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ${statusWarna}">
                            ${item.status_label}
                        </span>
                    </div>
                    <span class="text-[10px] text-gray-400">
                        Kategori: <strong class="text-gray-600">${item.kategori}</strong>
                    </span>
                </div>

                <p class="text-xs font-bold text-gray-900 leading-snug">
                    ${escapeHtml(item.judul_pertanyaan)}
                </p>

                <div class="flex items-center justify-between text-[10px] text-gray-500 pt-1">
                    <span>Diinput oleh: <strong>${escapeHtml(item.pembuat)}</strong></span>
                    <button type="button" class="btn-toggle-jawaban font-semibold text-[#741228] hover:underline" data-target="jawaban-${index}">
                        Lihat Jawaban ▾
                    </button>
                </div>

                <div id="jawaban-${index}" class="hidden mt-2 p-2.5 bg-gray-50 rounded-lg border border-gray-100 text-[11px] text-gray-700 leading-relaxed whitespace-pre-line">
                    ${escapeHtml(item.isi_jawaban)}
                </div>
            `;

            daftarMirip.appendChild(card);
        });

        // Event listener toggle intip jawaban
        daftarMirip.querySelectorAll('.btn-toggle-jawaban').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetEl = document.getElementById(targetId);
                if (targetEl) {
                    const isHidden = targetEl.classList.contains('hidden');
                    targetEl.classList.toggle('hidden');
                    this.textContent = isHidden ? 'Sembunyikan Jawaban ▴' : 'Lihat Jawaban ▾';
                }
            });
        });

        kemiripanWrapper.classList.remove('hidden');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
@endsection

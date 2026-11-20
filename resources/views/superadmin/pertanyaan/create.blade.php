@extends('layouts.superadmin')

@section('title', 'Buat Pengetahuan Baru - Super Admin ASK UBAYA')
@section('page_title', 'Buat Pengetahuan Baru')

@section('content')
<div class="space-y-6 w-full">

    <!-- Form Pembungkus Utama -->
    <form id="form-pengetahuan" action="{{ route('superadmin.pertanyaan.store') }}" method="POST">
        @csrf

        <!-- Input Tersembunyi untuk Status: Default 2 (Disetujui / langsung publish tanpa validator) -->
        <input type="hidden" name="status" id="input-status" value="{{ old('status', 2) }}">

        <!-- Sub-Bar Navigasi & Aksi Tombol -->
        <div class="flex items-center justify-between pb-2">
            <!-- Tombol Kembali -->
            <a href="{{ route('superadmin.pertanyaan.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Kelola Pengetahuan</span>
            </a>

            <!-- Tombol Aksi Simpan Draf & Publikasikan Langsung -->
            <div class="flex items-center gap-3">
                <!-- Simpan Draf -->
                <button type="button" 
                        onclick="submitDenganStatus(0)" 
                        class="btn-submit-action px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-2xl text-xs font-bold transition shadow-sm hover:shadow cursor-pointer">
                    Simpan Draf
                </button>

                <!-- Publikasikan / Terbitkan Langsung (Bypass Validator) -->
                <button type="button" 
                        onclick="submitDenganStatus(2)" 
                        class="btn-submit-action inline-flex items-center gap-2 px-6 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-md hover:shadow-lg cursor-pointer">
                    <svg class="w-4 h-4 text-rose-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Terbitkan Langsung</span>
                </button>
            </div>
        </div>

        <!-- Judul Halaman & Subjudul -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                Buat Pengetahuan Baru
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
                Sebagai Super Admin, pertanyaan yang Anda terbitkan langsung tampil di halaman mahasiswa tanpa harus melewati proses review Validator.
            </p>
        </div>

        <!-- Alert Error Validasi jika ada -->
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl mb-6 text-xs shadow-2xs">
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

        <!-- Form Utama (Single Card) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-gray-100 shadow-sm space-y-8">
            
            <!-- BAGIAN 1: Informasi Pertanyaan -->
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-[#741228] text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                        1
                    </div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Informasi Pertanyaan</h2>
                </div>

                <!-- Pertanyaan * -->
                <div>
                    <label for="judul_pertanyaan" class="block text-xs font-bold text-gray-800 mb-1">
                        Pertanyaan <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-gray-400 mb-2">
                        Tuliskan pertanyaan dengan bahasa yang jelas dan biasa ditanyakan oleh mahasiswa.
                    </p>
                    <input type="text" 
                           id="judul_pertanyaan" 
                           name="judul_pertanyaan" 
                           value="{{ old('judul_pertanyaan') }}" 
                           placeholder="Contoh: Bagaimana prosedur pengajuan beasiswa prestasi semester ini?" 
                           required 
                           autocomplete="off"
                           class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-2xs">

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

                <!-- Grid Kategori & Target Sasaran Fakultas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kategori Pertanyaan * -->
                    <div>
                        <label for="kategori_id" class="block text-xs font-bold text-gray-800 mb-1">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select id="kategori_id" 
                                name="kategori_id" 
                                required 
                                class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-2xs cursor-pointer">
                            <option value="">Pilih kategori pertanyaan</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target Sasaran Informasi (Fakultas) -->
                    <div>
                        <label for="fakultas" class="block text-xs font-bold text-gray-800 mb-1">
                            Target Sasaran Fakultas <span class="text-rose-500">*</span>
                        </label>
                        <select id="fakultas" 
                                name="fakultas" 
                                required
                                class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-2xs cursor-pointer">
                            <option value="umum" {{ old('fakultas', 'umum') == 'umum' ? 'selected' : '' }}>
                                Umum (Semua Fakultas)
                            </option>
                            @foreach($daftarFakultas as $kodeFak => $namaFak)
                                <option value="{{ $kodeFak }}" {{ old('fakultas') == $kodeFak ? 'selected' : '' }}>
                                    {{ $namaFak }} (Kode: {{ $kodeFak }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">
                            Pilih "Umum" untuk kebijakan universitas, atau pilih fakultas tertentu.
                        </p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- BAGIAN 2: Jawaban & Kata Kunci -->
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-[#741228] text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                        2
                    </div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Jawaban & Kata Kunci</h2>
                </div>

                <!-- Jawaban Terverifikasi * -->
                <div>
                    <label for="isi_jawaban" class="block text-xs font-bold text-gray-800 mb-1">
                        Jawaban Resmi <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="isi_jawaban" 
                              name="isi_jawaban" 
                              rows="6" 
                              placeholder="Tuliskan jawaban yang akurat, ringkas, dan mudah dipahami mahasiswa..." 
                              required 
                              class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-2xs resize-y leading-relaxed">{{ old('isi_jawaban') }}</textarea>
                </div>

                <!-- Kata Kunci (Tag System) -->
                <div>
                    <label for="input-tag" class="block text-xs font-bold text-gray-800 mb-1">
                        Kata Kunci / Tag <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>

                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="text" 
                               id="input-tag" 
                               list="saran-tag" 
                               placeholder="Ketik kata kunci lalu tekan Enter atau tombol Tambah" 
                               autocomplete="off" 
                               class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 text-xs sm:text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#741228]/20 focus:border-[#741228] transition shadow-2xs">
                        
                        <datalist id="saran-tag">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->nama_tag }}"></option>
                            @endforeach
                        </datalist>

                        <button type="button" 
                                id="tambah-tag" 
                                class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer">
                            Tambah
                        </button>
                    </div>
                    
                    <p class="text-[11px] text-gray-400 mb-3">
                        Kata kunci mempermudah mahasiswa menemukan jawaban ini di kotak pencarian.
                    </p>

                    <!-- Chip Tag Container -->
                    <div id="daftar-tag" class="flex flex-wrap gap-2 min-h-[32px]">
                        @foreach (old('tags', []) as $namaTag)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-rose-50 text-[#741228] border border-rose-200/80 shadow-2xs">
                                <span>{{ $namaTag }}</span>
                                <button type="button" class="hapus-tag text-rose-400 hover:text-rose-700 text-sm leading-none cursor-pointer">&times;</button>
                                <input type="hidden" name="tags[]" value="{{ $namaTag }}">
                            </span>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Tombol Aksi di Bagian Bawah Formulir -->
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                <a href="{{ route('superadmin.pertanyaan.index') }}" 
                   class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-2xl text-xs font-bold transition text-center border border-gray-200">
                    Batal
                </a>
                <button type="button" 
                        onclick="submitDenganStatus('draft')" 
                        class="px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-2xl text-xs font-bold transition shadow-sm hover:shadow text-center cursor-pointer">
                    Simpan Draf
                </button>
                <button type="button" 
                        onclick="submitDenganStatus('disetujui')" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#741228] hover:bg-[#5e0d1f] text-white rounded-2xl text-xs font-bold transition shadow-md hover:shadow-lg cursor-pointer">
                    <svg class="w-4 h-4 text-rose-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Publikasikan Langsung</span>
                </button>
            </div>

        </div>

    </form>

</div>
@endsection

@section('scripts')
<script>
    let adaKemiripanTerdeteksi = false;

    function submitDenganStatus(statusBaru) {
        if (adaKemiripanTerdeteksi) {
            alert('Pertanyaan tidak dapat disimpan karena terdeteksi serupa dengan pertanyaan yang sudah ada di database. Silakan ubah rumusan pertanyaan Anda.');
            return false;
        }
        document.getElementById('input-status').value = statusBaru;
        document.getElementById('form-pengetahuan').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const formPengetahuan = document.getElementById('form-pengetahuan');
        const inputJudul = document.getElementById('judul_pertanyaan');
        const kemiripanWrapper = document.getElementById('kemiripan-wrapper');
        const daftarMirip = document.getElementById('daftar-pertanyaan-mirip');
        let timerDebounce = null;

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
                const response = await fetch("{{ route('superadmin.pertanyaan.cekKemiripan') }}", {
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
                if (Number(item.status) === 2 || item.status === 'disetujui') {
                    statusWarna = 'bg-emerald-100 text-emerald-800';
                } else if (Number(item.status) === 1 || item.status === 'menunggu_validasi') {
                    statusWarna = 'bg-sky-100 text-sky-800';
                } else if (Number(item.status) === 3 || item.status === 'ditolak') {
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

        // Tag Handling
        const inputTag = document.getElementById('input-tag');
        const tombolTambah = document.getElementById('tambah-tag');
        const daftarTag = document.getElementById('daftar-tag');

        function buatChipTag(namaTag) {
            const span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-rose-50 text-[#741228] border border-rose-200/80 shadow-2xs';

            const teks = document.createElement('span');
            teks.textContent = namaTag;

            const tombolHapus = document.createElement('button');
            tombolHapus.type = 'button';
            tombolHapus.className = 'hapus-tag text-rose-400 hover:text-rose-700 text-sm leading-none ml-1 cursor-pointer';
            tombolHapus.innerHTML = '&times;';

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tags[]';
            hiddenInput.value = namaTag;

            span.appendChild(teks);
            span.appendChild(tombolHapus);
            span.appendChild(hiddenInput);

            return span;
        }

        function tambahTag() {
            const nilai = inputTag.value.trim().toLowerCase();
            if (nilai === '') return;

            const tagSudahAda = Array.from(daftarTag.querySelectorAll('input[type="hidden"]'))
                .some(input => input.value.toLowerCase() === nilai);

            if (tagSudahAda) {
                inputTag.value = '';
                return;
            }

            const chip = buatChipTag(nilai);
            daftarTag.appendChild(chip);
            inputTag.value = '';
        }

        if (tombolTambah) tombolTambah.addEventListener('click', tambahTag);

        if (inputTag) {
            inputTag.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    tambahTag();
                }
            });
        }

        if (daftarTag) {
            daftarTag.addEventListener('click', function (e) {
                if (e.target.classList.contains('hapus-tag')) {
                    e.target.closest('span').remove();
                }
            });
        }
    });
</script>
@endsection


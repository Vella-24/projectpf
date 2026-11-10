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
        return [
            'id' => $p->id,
            'kategori' => $categoryName,
            'kategori_upper' => strtoupper($categoryName),
            'judul' => $p->judul_pertanyaan,
            'jawaban' => $p->isi_jawaban,
            'status' => $p->status,
            'validator_id' => $p->validator_id,
            'validator_name' => $p->validator->name ?? null,
            'boleh_review' => $p->status === 'menunggu_validasi'
            && $p->validator_id !== null
            && (int) $p->validator_id === (int) session('user.id'),
            'status_label' => $p->status === 'menunggu_validasi' ? 'Menunggu' : ($p->status === 'disetujui' ? 'Disetujui' : ($p->status === 'ditolak' ? 'Perlu Revisi' : ucwords(str_replace('_', ' ', $p->status)))),
            'user' => $authorName,
            'catatan' => $p->catatan_validator ?? '',
            'waktu' => $timeString,
            'update_url' => route('validator.updateStatus', $p->id)
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
                        <option value="menunggu_validasi" {{ ($status ?? 'menunggu_validasi') === 'menunggu_validasi' ? 'selected' : '' }}>Perlu Ditinjau</option>
                        <option value="disetujui" {{ ($status ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ ($status ?? '') === 'ditolak' ? 'selected' : '' }}>Perlu Direvisi</option>
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
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $item['status'] === 'menunggu_validasi' ? 'text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md' : ($item['status'] === 'disetujui' ? 'text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md' : 'text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $item['status'] === 'menunggu_validasi' ? 'bg-amber-500' : ($item['status'] === 'disetujui' ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                            {{ $item['status_label'] }}
                        </span>
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
                <span id="detailStatusBadge" class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $firstItem['status'] === 'menunggu_validasi' ? 'text-amber-700 bg-amber-50 border-amber-200/60' : ($firstItem['status'] === 'disetujui' ? 'text-emerald-700 bg-emerald-50 border-emerald-200/60' : 'text-rose-700 bg-rose-50 border-rose-200/60') }} px-2.5 py-1 rounded-full border">
                    <span id="detailStatusDot" class="w-1.5 h-1.5 rounded-full {{ $firstItem['status'] === 'menunggu_validasi' ? 'bg-amber-500' : ($firstItem['status'] === 'disetujui' ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                    <span id="detailStatus">{{ $firstItem['status_label'] }}</span>
                </span>
            </div>

            <!-- Big Title -->
            <h2 id="detailJudul" class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug mb-2">
                {{ $firstItem['judul'] }}
            </h2>

            <!-- Sub Metadata -->
            <p id="detailMeta" class="text-xs text-gray-400 mb-6">
                Diajukan oleh <span class="text-gray-700 font-medium">{{ $firstItem['user'] }}</span> &bull; {{ $firstItem['waktu'] }}
            </p>

            <!-- Informasi siapa yang mengambil review -->
            <div id="detailValidatorInfo" class="mb-5 text-sm"></div>

            <!-- Section: Jawaban Yang Diajukan -->
            <div class="mb-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2.5">
                    JAWABAN YANG DIAJUKAN
                </h4>
                <div id="detailJawaban" class="text-sm leading-relaxed text-gray-700 bg-gray-50/50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
                    {{ $firstItem['jawaban'] }}
                </div>
            </div>

            <!-- Keterangan status kepemilikan review -->
            <div id="detailValidatorInfo"
                class="mb-5 text-sm font-semibold text-amber-600">
            </div>
            <!-- Form Keputusan Validator (Menggunakan Route updateStatus yang ada) -->
            <form id="formReview" action="{{ $firstItem['update_url'] }}" method="POST" class="hidden">
                @csrf
                @method('PUT')
                
                <!-- Hidden input status: akan diisi 'disetujui' atau 'ditolak' via button -->
                <input type="hidden" name="status" id="inputStatus" value="disetujui">
                <input type="hidden" name="redirect_status" value="{{ $status ?? 'menunggu_validasi' }}">

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
                    <button type="button" onclick="submitDecision('ditolak')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-[#FFF1F2] text-[#9F1239] hover:bg-[#FFE4E6] border border-[#FECDD3] transition cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4 text-[#9F1239]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Minta Revisi</span>
                    </button>

                    <!-- Button Setujui & Terbitkan (Disetujui) -->
                    <button type="button" onclick="submitDecision('disetujui')"
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

        const elJawaban = document.getElementById('detailJawaban');
        if (elJawaban) elJawaban.textContent = item.jawaban;

        const elCatatan = document.getElementById('detailCatatan');
        if (elCatatan) elCatatan.value = item.catatan || '';

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

            if (item.status !== 'menunggu_validasi') {

                validatorInfo.textContent =
                    'FAQ ini sudah selesai divalidasi.';

                validatorInfo.className =
                    'mb-5 text-sm font-semibold text-gray-500';

            } else if (bolehReview) {

                validatorInfo.textContent =
                    'Kamu sedang mereview pertanyaan ini.';

                validatorInfo.className =
                    'mb-5 text-sm font-semibold text-emerald-600';

            } else if (item.validator_id != null) {

                validatorInfo.textContent =
                    'Sedang direview oleh ' +
                    (item.validator_name || 'Validator');

                validatorInfo.className =
                    'mb-5 text-sm font-semibold text-blue-600';

            } else {

                validatorInfo.textContent =
                    'Belum ada Validator yang mengambil review. Klik Ambil untuk Direview terlebih dahulu.';

                validatorInfo.className =
                    'mb-5 text-sm font-semibold text-amber-600';
            }
        }

        // Status Badge styling
        const badge = document.getElementById('detailStatusBadge');
        const dot = document.getElementById('detailStatusDot');
        if (badge && dot) {
            if (item.status === 'menunggu_validasi') {
                badge.className = "inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200/60";
                dot.className = "w-1.5 h-1.5 rounded-full bg-amber-500";
            } else if (item.status === 'disetujui') {
                badge.className = "inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60";
                dot.className = "w-1.5 h-1.5 rounded-full bg-emerald-500";
            } else {
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
        
        if (status === 'ditolak') {
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

    if (activeId !== null) {
            selectItem(activeId);
        }
</script>
@endsection

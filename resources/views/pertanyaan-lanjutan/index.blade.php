
@extends('layouts.data-entry')

@section('title', 'ASK UBAYA - Pertanyaan Mahasiswa')
@section('page_title', 'Pertanyaan Mahasiswa')

@section('content')

<div class="space-y-6">

    <!-- HEADER HALAMAN -->
    <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
            Pertanyaan Mahasiswa
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Kelola pertanyaan lanjutan yang dikirim mahasiswa melalui portal ASK UBAYA.
        </p>
    </div>

    <!-- NOTIFIKASI SUKSES -->
    @if (session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl text-sm font-medium">
            {{ session('sukses') }}
        </div>
    @endif

    <!-- NOTIFIKASI ERROR -->
    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- STATISTIK RINGKAS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- TOTAL PERTANYAAN -->
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-500">
                    Total Pertanyaan
                </span>
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600">
                    <i class="fas fa-comments"></i>
                </div>
            </div>

            <p class="text-3xl font-extrabold text-gray-900 mt-3">
                {{ $totalPertanyaan }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Seluruh pertanyaan lanjutan
            </p>
        </div>

        <!-- BELUM DITANGANI -->
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-500">
                    Belum Ditangani
                </span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <p class="text-3xl font-extrabold text-amber-600 mt-3">
                {{ $totalBaru }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Menunggu diambil Data Entry
            </p>
        </div>

        <!-- SEDANG DITANGANI -->
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-500">
                    Sedang Ditangani
                </span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <p class="text-3xl font-extrabold text-blue-600 mt-3">
                {{ $totalDiproses }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Sudah diambil oleh petugas
            </p>
        </div>

    </div>

    <!-- DAFTAR PERTANYAAN -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- JUDUL SECTION -->
        <div class="px-5 sm:px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">
                Daftar Pertanyaan Masuk
            </h3>
            <p class="text-xs text-gray-500 mt-1">
                Pertanyaan tersedia untuk seluruh Data Entry.
                Hanya satu petugas yang dapat mengambil setiap pertanyaan.
            </p>
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
                            <div class="mt-3 bg-[#FFF7F8] border border-rose-100 rounded-xl p-3.5">
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

                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-user text-gray-400 w-4"></i>
                                <span>
                                    {{ $item->nama_mahasiswa }}
                                </span>
                            </div>

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
                                        Ambil Pertanyaan
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
                                            {{ $item->faqBaru ? 'Lanjutkan Jawaban' : 'Jawab Pertanyaan' }}
                                        </a>
                                    @endif

                            @elseif ($item->status === 'sedang_ditangani')

                                <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-xs font-semibold">
                                    <i class="fas fa-lock"></i>
                                    Sudah Diambil
                                </span>

                            @elseif ($item->status === 'dijawab')

                                <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-semibold">
                                    <i class="fas fa-check-double"></i>
                                    Selesai Dijawab
                                </span>

                            @else

                                <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-xs font-semibold">
                                    Ditutup
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <!-- EMPTY STATE -->
                <div class="py-16 px-5 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-inbox text-2xl text-gray-300"></i>
                    </div>

                    <h4 class="text-sm font-bold text-gray-700">
                        Belum Ada Pertanyaan Mahasiswa
                    </h4>

                    <p class="text-xs text-gray-400 mt-2">
                        Pertanyaan lanjutan dari mahasiswa akan muncul di sini.
                    </p>
                </div>

            @endforelse

        </div>

        <!-- PAGINATION -->
        @if ($pertanyaans->hasPages())
            <div class="px-5 sm:px-6 py-4 border-t border-gray-100">
                {{ $pertanyaans->links() }}
            </div>
        @endif

    </div>

</div>

@endsection

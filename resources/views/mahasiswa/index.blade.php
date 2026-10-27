<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASK UBAYA - Student Knowledge Base</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ubaya: {
                            50: '#FDF2F4',
                            100: '#FCE7EB',
                            200: '#F9D0D8',
                            300: '#F4A9B8',
                            400: '#EC738C',
                            500: '#DF4264',
                            600: '#CA2448',
                            700: '#A91A39',
                            800: '#8C1832',
                            900: '#77182E',
                            950: '#460815',
                        },
                        maroon: {
                            base: '#741228',
                            dark: '#580A1C',
                            deep: '#420614',
                            text: '#8B1538',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF9F9;
        }

        /* Subtle Hero Grid Background */
        .hero-pattern {
            background-color: #620B1F;
            background-image: 
                radial-gradient(circle at 100% 20%, rgba(138, 20, 48, 0.4) 0%, transparent 60%),
                linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 100% 100%, 36px 36px, 36px 36px;
        }

        /* Accordion transition */
        .accordion-content {
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #d4a7b1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a91a39;
        }
    </style>
</head>
<body class="text-gray-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Flash Alert Notification -->
    @if(session('sukses'))
        <div id="toastSuccess" class="fixed top-20 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3.5 rounded-2xl shadow-xl transition-all duration-300">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium">{{ session('sukses') }}</span>
            <button onclick="document.getElementById('toastSuccess').remove()" class="ml-2 hover:opacity-75">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Top Navigation Bar -->
    <header class="bg-white/95 backdrop-blur sticky top-0 z-40 border-b border-gray-150 transition-all shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('mahasiswa.index') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#7C1224] to-[#5C0A19] flex items-center justify-center text-white shadow-md shadow-maroon-base/20 group-hover:scale-105 transition-transform duration-200">
                    <!-- Mortarboard / Student Icon -->
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.94 8.13 22 12 22C15.87 22 19 19.94 19 17.18V13.18L12 17L5 13.18Z"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-black tracking-tight text-gray-900 group-hover:text-[#7C1224] transition-colors leading-none">ASK UBAYA</span>
                    <span class="text-[9.5px] font-bold text-gray-400 tracking-[0.25em] uppercase mt-1">STUDENT KNOWLEDGE</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('mahasiswa.index') }}" class="text-sm font-semibold text-[#7C1224] hover:text-[#5C0A19] transition-colors">Beranda</a>
                <a href="#topik" class="text-sm font-medium text-gray-600 hover:text-[#7C1224] transition-colors">Jelajahi Topik</a>
                <a href="#tentang" class="text-sm font-medium text-gray-600 hover:text-[#7C1224] transition-colors">Tentang</a>
            </nav>

            <!-- Portal Staf Button & Trigger -->
            <div class="relative">
                <a href="/login" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 shadow-xs transition-all active:scale-95">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Portal Staf</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="hero-pattern relative text-white pt-16 pb-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Concentric Circular Lines Decorative Background -->
            <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full border border-white/10 pointer-events-none hidden lg:block"></div>
            <div class="absolute -right-48 -top-48 w-[540px] h-[540px] rounded-full border border-white/5 pointer-events-none hidden lg:block"></div>
            <div class="absolute -right-72 -top-72 w-[720px] h-[720px] rounded-full border border-white/[0.03] pointer-events-none hidden lg:block"></div>

            <div class="max-w-4xl mx-auto text-center relative z-10">
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-white/90 text-xs font-semibold tracking-wider uppercase mb-7 shadow-sm">
                    <span class="text-amber-300 text-sm">✦</span>
                    <span>PUSAT INFORMASI MAHASISWA UBAYA</span>
                </div>

                <!-- Main Hero Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.15] mb-5">
                    Apa yang ingin kamu ketahui?
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-rose-100/90 max-w-2xl mx-auto font-normal leading-relaxed mb-9">
                    Temukan jawaban tepercaya seputar perkuliahan dan layanan kampus dalam hitungan detik.
                </p>

                <!-- Search Form -->
                <form action="{{ route('mahasiswa.index') }}" method="GET" class="max-w-2xl mx-auto">
                    <!-- Preserve category filter if active -->
                    @if(request('kategori_id'))
                        <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="bg-white rounded-2xl md:rounded-full p-2 pl-5 shadow-2xl shadow-black/25 flex flex-col sm:flex-row items-center gap-2 border border-white/30 focus-within:ring-4 focus-within:ring-white/20 transition-all">
                        <div class="flex items-center gap-3 w-full">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}" 
                                placeholder='Ketik pertanyaanmu, misalnya "cara mengajukan cuti"' 
                                class="w-full bg-transparent text-gray-800 placeholder-gray-400 text-sm sm:text-base focus:outline-none py-2"
                                autocomplete="off"
                            >
                            @if(request('q'))
                                <a href="{{ route('mahasiswa.index', request()->except('q')) }}" class="text-gray-400 hover:text-gray-600 px-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-xl md:rounded-full bg-[#7C1224] hover:bg-[#5C0A19] text-white font-semibold text-sm transition-all duration-200 shadow-md shadow-maroon-base/30 whitespace-nowrap active:scale-95">
                            Cari Jawaban
                        </button>
                    </div>
                </form>

                <!-- Popular Search Chips -->
                <div class="mt-6 flex items-center justify-center flex-wrap gap-2 text-xs sm:text-sm text-white/80">
                    <span class="font-normal text-white/70">Populer:</span>
                    <a href="{{ route('mahasiswa.index', ['q' => 'cuti']) }}" class="px-3.5 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white/90 font-medium transition-all text-xs">Pengajuan cuti</a>
                    <a href="{{ route('mahasiswa.index', ['q' => 'presensi']) }}" class="px-3.5 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white/90 font-medium transition-all text-xs">Presensi</a>
                    <a href="{{ route('mahasiswa.index', ['q' => 'wisuda']) }}" class="px-3.5 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white/90 font-medium transition-all text-xs">Jadwal wisuda</a>
                </div>
            </div>
        </section>

        <!-- Topic Categories Bar (Overlapping Card) -->
        <section id="topik" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-lg shadow-gray-200/50 border border-gray-150">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                        // Mapping categories with mockup specific colors and icons
                        $categoryIcons = [
                            'Akademik' => [
                                'bg' => 'bg-rose-50 text-rose-600 group-hover:bg-rose-100',
                                'active_bg' => 'bg-rose-100 text-rose-700 ring-2 ring-rose-500',
                                'count_default' => '18 artikel',
                                'svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>'
                            ],
                            'Perkuliahan' => [
                                'bg' => 'bg-purple-50 text-purple-600 group-hover:bg-purple-100',
                                'active_bg' => 'bg-purple-100 text-purple-700 ring-2 ring-purple-500',
                                'count_default' => '64 artikel',
                                'svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>'
                            ],
                            'Keuangan' => [
                                'bg' => 'bg-amber-50 text-amber-600 group-hover:bg-amber-100',
                                'active_bg' => 'bg-amber-100 text-amber-700 ring-2 ring-amber-500',
                                'count_default' => '36 artikel',
                                'svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                            ],
                            'Kemahasiswaan' => [
                                'bg' => 'bg-teal-50 text-teal-600 group-hover:bg-teal-100',
                                'active_bg' => 'bg-teal-100 text-teal-700 ring-2 ring-teal-500',
                                'count_default' => '52 artikel',
                                'svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
                            ],
                            'Wisuda' => [
                                'bg' => 'bg-rose-50 text-rose-600 group-hover:bg-rose-100',
                                'active_bg' => 'bg-rose-100 text-rose-700 ring-2 ring-rose-500',
                                'count_default' => '29 artikel',
                                'svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                            ],
                            'Layanan Lain' => [
                                'bg' => 'bg-sky-50 text-sky-600 group-hover:bg-sky-100',
                                'active_bg' => 'bg-sky-100 text-sky-700 ring-2 ring-sky-500',
                                'count_default' => '41 artikel',
                                'svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'
                            ],
                        ];

                        // Target 6 categories as in screenshot
                        $targetCategories = ['Akademik', 'Perkuliahan', 'Keuangan', 'Kemahasiswaan', 'Wisuda', 'Layanan Lain'];
                    @endphp

                    @foreach($targetCategories as $targetName)
                        @php
                            $catModel = $kategoris->first(function($k) use ($targetName) {
                                return stripos($k->nama_kategori, $targetName) !== false;
                            });
                            $catId = $catModel ? $catModel->id : null;
                            $isSelected = $catId && request('kategori_id') == $catId;
                            $style = $categoryIcons[$targetName] ?? $categoryIcons['Layanan Lain'];
                            $countText = $catModel ? ($catModel->pertanyaans_count . ' artikel') : $style['count_default'];
                            $linkHref = $isSelected 
                                ? route('mahasiswa.index', request()->except('kategori_id'))
                                : ($catId ? route('mahasiswa.index', array_merge(request()->query(), ['kategori_id' => $catId])) : '#');
                        @endphp

                        <a href="{{ $linkHref }}" class="group flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl hover:bg-gray-50/80 transition-all duration-200 {{ $isSelected ? 'bg-rose-50/50 ring-2 ring-[#7C1224]' : '' }}">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-3 transition-transform duration-200 group-hover:scale-110 {{ $isSelected ? $style['active_bg'] : $style['bg'] }}">
                                {!! $style['svg'] !!}
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-[#7C1224] transition-colors line-clamp-1">
                                {{ $targetName }}
                            </span>
                            <span class="text-xs text-gray-400 mt-0.5 font-medium">
                                {{ $countText }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Main Content Area: Questions & Sidebar -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-20">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-gray-200/70 mb-8">
                <div>
                    <span class="text-xs font-black tracking-[0.2em] uppercase text-[#8B1538] block mb-1">
                        PALING SERING DITANYAKAN
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                        Jawaban yang mungkin kamu cari
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    @if(request('q') || request('kategori_id') || request('status'))
                        <a href="{{ route('mahasiswa.index') }}" class="text-xs font-semibold text-rose-700 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-full transition-colors inline-flex items-center gap-1.5">
                            <span>Reset Filter</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                    <span class="text-sm font-medium text-gray-500 whitespace-nowrap">
                        {{ $pertanyaans->total() }} jawaban ditemukan
                    </span>
                </div>
            </div>

            <!-- Two-Column Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Accordion Questions (8 cols) -->
                <div class="lg:col-span-8 space-y-4">
                    @forelse($pertanyaans as $index => $item)
                        @php
                            $catName = $item->kategori->nama_kategori ?? 'UMUM';
                            
                            // Determine category icon and styling
                            $iconColorClass = 'bg-rose-50 text-rose-600';
                            $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>';

                            if (stripos($catName, 'keuangan') !== false) {
                                $iconColorClass = 'bg-amber-50 text-amber-600';
                                $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                            } elseif (stripos($catName, 'kemahasiswaan') !== false) {
                                $iconColorClass = 'bg-teal-50 text-teal-600';
                                $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
                            } elseif (stripos($catName, 'perkuliahan') !== false) {
                                $iconColorClass = 'bg-purple-50 text-purple-600';
                                $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>';
                            } elseif (stripos($catName, 'wisuda') !== false) {
                                $iconColorClass = 'bg-rose-50 text-rose-600';
                                $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
                            }

                            // Match Image 1: The very first question is expanded by default
                            $isOpen = ($index === 0 && !request('q') && !request('kategori_id'));
                        @endphp

                        <div class="faq-item bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-gray-300 transition-all duration-200 overflow-hidden" id="faq-{{ $item->id }}">
                            <!-- Clickable Header Row -->
                            <button 
                                type="button"
                                onclick="toggleAccordion('{{ $item->id }}')" 
                                class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4 cursor-pointer focus:outline-none group"
                            >
                                <div class="flex items-center gap-4 min-w-0">
                                    <!-- Category Icon Box -->
                                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 {{ $iconColorClass }} group-hover:scale-105 transition-transform">
                                        {!! $iconSvg !!}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[11px] font-bold text-[#8B1538] uppercase tracking-wider block mb-0.5">
                                            {{ strtoupper($catName) }}
                                        </span>
                                        <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-[#7C1224] transition-colors leading-snug">
                                            {{ $item->judul_pertanyaan }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="chevron-icon ml-2 flex-shrink-0 text-gray-400 group-hover:text-gray-700 transition-transform duration-200 {{ $isOpen ? 'rotate-180' : '' }}" id="chevron-{{ $item->id }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>

                            <!-- Collapsible Content -->
                            <div 
                                id="body-{{ $item->id }}" 
                                class="accordion-content {{ $isOpen ? 'block' : 'hidden' }}"
                            >
                                <div class="px-5 sm:px-6 pt-1 pb-6 text-gray-600 text-sm sm:text-[15px] leading-relaxed sm:pl-20">
                                    {{ $item->isi_jawaban }}

                                    <!-- Tags if present -->
                                    @if($item->tags && $item->tags->count() > 0)
                                        <div class="mt-4 flex flex-wrap gap-1.5 items-center">
                                            <span class="text-xs text-gray-400">Kata Kunci:</span>
                                            @foreach($item->tags as $t)
                                                <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-medium">#{{ $t->nama_tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Accordion Footer Bar -->
                                <div class="bg-gray-50/70 border-t border-gray-100 px-5 sm:px-6 py-3.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:pl-20 text-xs text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <span>Diperbarui {{ $item->updated_at ? $item->updated_at->diffForHumans() : '2 hari lalu' }}</span>
                                        <span>·</span>
                                        <span class="text-gray-600 font-medium">{{ 35 + ($item->id * 7) }} orang terbantu</span>
                                    </div>
                                    <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                                        <span class="text-gray-500 font-medium">Apakah membantu?</span>
                                        <div class="flex items-center gap-1.5" id="feedback-group-{{ $item->id }}">
                                            <button 
                                                type="button" 
                                                onclick="submitFeedback('{{ $item->id }}', 'yes')" 
                                                class="feedback-btn-yes px-3 py-1 rounded-full border border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-white text-xs font-semibold inline-flex items-center gap-1 transition shadow-2xs"
                                            >
                                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Ya</span>
                                            </button>
                                            <button 
                                                type="button" 
                                                onclick="submitFeedback('{{ $item->id }}', 'no')" 
                                                class="feedback-btn-no px-3 py-1 rounded-full border border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-white text-xs font-semibold transition shadow-2xs"
                                            >
                                                Tidak
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl p-12 text-center border border-gray-200">
                            <div class="w-16 h-16 rounded-full bg-rose-50 text-[#7C1224] flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Pertanyaan tidak ditemukan</h3>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                                Belum ada jawaban yang cocok dengan pencarian kata kunci atau filter kamu.
                            </p>
                            <a href="{{ route('mahasiswa.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#7C1224] text-white text-sm font-semibold hover:bg-[#5C0A19] transition">
                                Lihat Semua Pertanyaan
                            </a>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($pertanyaans->hasPages())
                        <div class="pt-6">
                            {{ $pertanyaans->links() }}
                        </div>
                    @endif
                </div>

                <!-- Right Column: Sidebar (4 cols) -->
                <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                    
                    <!-- Sidebar Card 1: Student NRP Profile Card -->
                    <div class="bg-gradient-to-b from-white to-[#FDF8F9] rounded-3xl p-6 sm:p-7 border border-gray-200/90 shadow-xs relative overflow-hidden">
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#7C1224] flex items-center justify-center shadow-xs">
                                <!-- Graduation Cap Icon -->
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.94 8.13 22 12 22C15.87 22 19 19.94 19 17.18V13.18L12 17L5 13.18Z"/>
                                </svg>
                            </div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>NRP Aktif</span>
                            </div>
                        </div>

                        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight leading-snug">
                            Halo, mahasiswa <span id="studentNrpDisplay">789</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                            Informasi saat ini dipersonalisasi untuk NRP <span id="studentNrpMasked">••••••789</span>.
                        </p>

                        <button 
                            type="button" 
                            onclick="openNrpModal()"
                            class="w-full mt-6 py-2.5 px-4 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 rounded-xl text-sm font-bold text-gray-700 transition shadow-xs flex items-center justify-center gap-2 active:scale-98"
                        >
                            <span>Ganti NRP</span>
                        </button>
                    </div>

                    <!-- Sidebar Card 2: Question Prompt CTA Card -->
                    <div class="bg-white rounded-3xl p-7 border border-gray-200/90 shadow-xs text-center flex flex-col items-center">
                        <!-- Thick Burgundy Question Mark Circle Illustration -->
                        <div class="w-36 h-36 mx-auto my-3 flex items-center justify-center">
                            <svg class="w-36 h-36 text-[#7C1224]" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="80" cy="80" r="66" stroke="currentColor" stroke-width="13" fill="transparent"/>
                                <path d="M68 64C68 56 73 50 81 50C88 50 94 55 94 62C94 68 90 71 85 75C79 79 76 83 76 89H86C86 85 88 83 93 79C99 74 104 69 104 60C104 48 93 40 80 40C67 40 58 49 58 64H68ZM81 106C84.3 106 87 103.3 87 100C87 96.7 84.3 94 81 94C77.7 94 75 96.7 75 100C75 103.3 77.7 106 81 106Z" fill="currentColor"/>
                            </svg>
                        </div>

                        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-3">
                            Belum menemukan jawaban?
                        </h3>
                        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                            Kirimkan pertanyaan baru untuk membantu kami memperkaya pusat informasi.
                        </p>

                        <a 
                            href="{{ route('pertanyaan.create') }}" 
                            class="w-full mt-6 py-3 px-4 rounded-xl border border-gray-200 hover:border-[#7C1224] hover:text-[#7C1224] text-sm font-bold text-gray-800 bg-white transition shadow-xs inline-flex items-center justify-center gap-2 active:scale-98">
                            <span>Ajukan pertanyaan</span>
                        </a>
                    </div>

                </aside>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="tentang" class="bg-white border-t border-gray-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Brand -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#7C1224] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.94 8.13 22 12 22C15.87 22 19 19.94 19 17.18V13.18L12 17L5 13.18Z"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-black tracking-tight text-gray-900 leading-none">ASK UBAYA</span>
                    <span class="text-[8.5px] font-bold text-gray-400 tracking-[0.2em] uppercase mt-0.5">STUDENT KNOWLEDGE</span>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-xs sm:text-sm text-gray-500 text-center md:text-right font-medium">
                &copy; {{ date('Y') }} Universitas Surabaya · Direktorat Administrasi Akademik
            </div>
        </div>
    </footer>

    <!-- Modal: Ganti NRP Mahasiswa -->
    <div id="modalNrp" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-gray-150 transform transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#7C1224] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <button type="button" onclick="closeNrpModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900">Personalisasi NRP Mahasiswa</h3>
            <p class="text-sm text-gray-500 mt-1">
                Masukkan NRP kamu untuk menyesuaikan informasi dan riwayat layanan studi.
            </p>

            <form onsubmit="saveNrp(event)" class="mt-5 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor Registrasi Pokok (NRP)</label>
                    <input 
                        type="text" 
                        id="inputNrp" 
                        placeholder="Contoh: 160421789" 
                        maxlength="15" 
                        required 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#7C1224] text-gray-900 font-semibold"
                    >
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeNrpModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#7C1224] hover:bg-[#5C0A19] text-white font-semibold text-sm transition shadow-sm">
                        Simpan NRP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Portal Staf (Admin & Data Entry) -->
    <!-- <div id="modalStaff" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-gray-150 transform transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-[#7C1224] text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 leading-tight">Portal Staf UBAYA</h3>
                        <span class="text-xs text-gray-500 font-medium">Manajemen FAQ & Data Entry</span>
                    </div>
                </div>
                <button type="button" onclick="toggleStaffModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                Akses cepat untuk staf data entry dalam mengelola pertanyaan, memvalidasi konten, dan mengatur kategori.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                <a href="{{ route('pertanyaan.create') }}" class="p-4 rounded-2xl border border-gray-200 hover:border-[#7C1224] hover:bg-rose-50/30 transition group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-[#7C1224] flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 group-hover:text-[#7C1224] text-sm">Tambah Pertanyaan</div>
                        <div class="text-xs text-gray-400 mt-0.5">Input FAQ baru ke sistem</div>
                    </div>
                </a>

                <a href="{{ route('kategori.index') }}" class="p-4 rounded-2xl border border-gray-200 hover:border-[#7C1224] hover:bg-rose-50/30 transition group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 group-hover:text-[#7C1224] text-sm">Kelola Kategori</div>
                        <div class="text-xs text-gray-400 mt-0.5">Atur bidang & topik</div>
                    </div>
                </a>
            </div>

            <-- Filter Status FAQ -->
            <!-- <div class="pt-4 border-t border-gray-150">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2.5">Filter Berdasarkan Status:</span>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pertanyaan.index') }}" class="px-3 py-1 rounded-full text-xs font-semibold {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Semua Status</a>
                    <a href="{{ route('pertanyaan.index', ['status' => 'disetujui']) }}" class="px-3 py-1 rounded-full text-xs font-semibold {{ request('status') == 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">Disetujui</a>
                    <a href="{{ route('pertanyaan.index', ['status' => 'menunggu_validasi']) }}" class="px-3 py-1 rounded-full text-xs font-semibold {{ request('status') == 'menunggu_validasi' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">Menunggu</a>
                    <a href="{{ route('pertanyaan.index', ['status' => 'draft']) }}" class="px-3 py-1 rounded-full text-xs font-semibold {{ request('status') == 'draft' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Draft</a>
                    <a href="{{ route('pertanyaan.index', ['status' => 'ditolak']) }}" class="px-3 py-1 rounded-full text-xs font-semibold {{ request('status') == 'ditolak' ? 'bg-red-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">Ditolak</a>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Scripts for Accordion, Feedback, and NRP Customization -->
    <script>
        // Toggle Accordion functionality
        function toggleAccordion(id) {
            const body = document.getElementById('body-' + id);
            const chevron = document.getElementById('chevron-' + id);

            if (body.classList.contains('hidden')) {
                body.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // Student NRP Personalization
        function loadNrp() {
            const savedNrp = localStorage.getItem('askubaya_nrp') || '789';
            updateNrpUI(savedNrp);
        }

        function updateNrpUI(nrp) {
            const last3 = nrp.length >= 3 ? nrp.slice(-3) : nrp;
            const masked = nrp.length > 3 ? '••••••' + last3 : nrp;

            const displayEl = document.getElementById('studentNrpDisplay');
            const maskedEl = document.getElementById('studentNrpMasked');

            if (displayEl) displayEl.innerText = last3;
            if (maskedEl) maskedEl.innerText = masked;
        }

        function openNrpModal() {
            const modal = document.getElementById('modalNrp');
            const input = document.getElementById('inputNrp');
            input.value = localStorage.getItem('askubaya_nrp') || '160421789';
            modal.classList.remove('hidden');
        }

        function closeNrpModal() {
            document.getElementById('modalNrp').classList.add('hidden');
        }

        function saveNrp(event) {
            event.preventDefault();
            const val = document.getElementById('inputNrp').value.trim();
            if (val) {
                localStorage.setItem('askubaya_nrp', val);
                updateNrpUI(val);
                closeNrpModal();
                showNotification('NRP berhasil diperbarui!');
            }
        }

        // Staff Portal Modal
        function toggleStaffModal() {
            const modal = document.getElementById('modalStaff');
            modal.classList.toggle('hidden');
        }

        // Feedback Handler
        function submitFeedback(id, type) {
            const container = document.getElementById('feedback-group-' + id);
            if (!container) return;

            if (type === 'yes') {
                container.innerHTML = `
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-bold text-xs bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Terima kasih!</span>
                    </span>
                `;
                showNotification('Terima kasih atas tanggapan positifmu!');
            } else {
                container.innerHTML = `
                    <span class="inline-flex items-center gap-1 text-gray-600 font-medium text-xs bg-gray-100 px-2.5 py-1 rounded-full">
                        <span>Masukan dicatat</span>
                    </span>
                `;
                showNotification('Terima kasih, masukanmu akan membantu kami berbenah.');
            }
        }

        // Toast Notification Helper
        function showNotification(message) {
            const existing = document.getElementById('floatingToast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'floatingToast';
            toast.className = 'fixed bottom-6 right-6 z-50 flex items-center gap-2 bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold transition-all duration-300';
            toast.innerHTML = `
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>${message}</span>
            `;

            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Auto close modals when clicking backdrop
        window.addEventListener('click', function(e) {
            const nrpModal = document.getElementById('modalNrp');
            const staffModal = document.getElementById('modalStaff');
            if (e.target === nrpModal) closeNrpModal();
            if (e.target === staffModal) toggleStaffModal();
        });

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', function() {
            loadNrp();
        });
    </script>
</body>
</html>

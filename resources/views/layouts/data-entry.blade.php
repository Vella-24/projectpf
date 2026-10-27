<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASK UBAYA - Data Entry')</title>

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
                        brand: {
                            sidebar: '#3a0714',
                            sidebarHover: '#4e0d1e',
                            maroon: '#741228',
                            maroonHover: '#5e0d1f',
                            lightBg: '#FAF8F8',
                            accent: '#EC738C',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF8F8;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
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
    @yield('styles')
</head>
<body class="text-gray-800 antialiased min-h-screen flex bg-[#FAF8F8] overflow-x-hidden">

    <!-- Sidebar Kiri (Deep Maroon - Sticky agar selalu terlihat & berada di dalam flex flow) -->
    <aside class="w-64 bg-[#3a0714] text-white flex flex-col justify-between h-screen sticky top-0 shrink-0 z-30 shadow-xl">
        <div class="p-5 flex flex-col h-full justify-between overflow-y-auto">
            <div>
                <!-- Brand Logo ASK UBAYA -->
                <div class="flex items-center gap-3 px-2 py-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0 border border-white/10 shadow-sm">
                        <!-- Graduation Cap Icon -->
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14t1.5 2 1.5 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-base font-extrabold tracking-wide text-white leading-tight">ASK UBAYA</h1>
                        <p class="text-[10px] tracking-wider text-rose-200/60 font-semibold uppercase">STUDENT KNOWLEDGE</p>
                    </div>
                </div>

                <!-- User Profile Card -->
                <div class="bg-white/10 rounded-2xl p-3 flex items-center gap-3 border border-white/10 mb-6">
                    <div class="w-10 h-10 rounded-full bg-[#82142d] flex items-center justify-center font-bold text-white text-sm shadow-inner shrink-0">
                        DE
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ session('user.name') ?? 'Staff Data Entry' }}</p>
                        <p class="text-[11px] text-rose-200/70 capitalize">{{ str_replace('_', ' ', session('user.role') ?? 'Data Entry') }}</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-white/40 px-3 mb-2">MENU UTAMA</p>

                    <!-- Beranda / Konten -->
                    <a href="{{ route('pertanyaan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('pertanyaan.index') ? 'bg-white/15 text-white shadow-sm' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                        <!-- Grid Icon -->
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1.5" />
                            <rect x="14" y="3" width="7" height="7" rx="1.5" />
                            <rect x="14" y="14" width="7" height="7" rx="1.5" />
                            <rect x="3" y="14" width="7" height="7" rx="1.5" />
                        </svg>
                        <span>Beranda</span>
                    </a>

                    <!-- Konten Saya -->
                    <a href="{{ route('pertanyaan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-white/75 hover:bg-white/10 hover:text-white">
                        <!-- Document Icon -->
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Konten Saya</span>
                    </a>
                </div>
            </div>

            <!-- Tombol Keluar (Logout) -->
            <div class="pt-4 border-t border-white/10">
                <a href="/logout" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-rose-200/75 hover:text-white hover:bg-white/10 transition-colors">
                    <!-- Logout Icon -->
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area (min-w-0 flex-1 agar mengisi sisa layar pas 100% tanpa geser ke kanan) -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        
        <!-- Top Navbar -->
        <header class="h-18 px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between border-b border-gray-100 bg-[#FAF8F8]/90 backdrop-blur-md sticky top-0 z-20">
            <div>
                <h2 class="text-lg font-bold text-gray-900 leading-tight">@yield('page_title', 'Beranda')</h2>
                <p class="text-xs text-gray-400 font-medium">
                    Data Entry &bull; 
                    @php
                        $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                        $bulanIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        $now = now();
                        $tglFormatted = ($hariIndo[$now->format('l')] ?? $now->format('l')) . ', ' . $now->format('j') . ' ' . ($bulanIndo[(int)$now->format('n')] ?? $now->format('F')) . ' ' . $now->format('Y');
                    @endphp
                    {{ $tglFormatted }}
                </p>
            </div>

            <!-- Header Right Section -->
            <div class="flex items-center gap-4">
                <!-- Notification Bell -->
                <button type="button" class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-700 shadow-sm transition hover:bg-gray-50 relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </button>

                <!-- Profile Info Pill -->
                <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
                    <div class="w-9 h-9 rounded-full bg-rose-100 text-[#741228] font-bold text-xs flex items-center justify-center border border-rose-200">
                        DE
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-bold text-gray-800 leading-tight">{{ session('user.name') ?? 'Data Entry UBAYA' }}</p>
                        <p class="text-[11px] text-gray-400 capitalize">{{ str_replace('_', ' ', session('user.role') ?? 'Data Entry') }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('sukses'))
                <div id="toastSuccess" class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm mb-4 transition-all">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('sukses') }}</span>
                    </div>
                    <button onclick="document.getElementById('toastSuccess').remove()" class="text-emerald-500 hover:text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="toastError" class="flex items-center justify-between gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-sm mb-4 transition-all">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="document.getElementById('toastError').remove()" class="text-rose-500 hover:text-rose-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-12">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>

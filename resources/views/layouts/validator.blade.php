<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Validasi Konten - ASK UBAYA')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

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
            width: 5px;
            height: 5px;
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
<body class="text-gray-800 antialiased min-h-screen bg-[#FAF8F8] flex overflow-x-hidden">

@php
    $userName = session('user.name') ?? 'Validator UBAYA';
    $userRole = session('user.role') ?? 'Validator';
    $nameParts = explode(' ', trim($userName));
    $initials = '';
    if (count($nameParts) >= 2) {
        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
    } else {
        $initials = strtoupper(substr($userName, 0, 2));
    }
    if (!$initials) $initials = 'VU';
@endphp

    <!-- Sidebar Kiri Validator (Interaktif: Muncul saat profil diklik, tutup via silang/profil, konten mengecil/melebar) -->
    <aside id="leftSidebar" 
           class="bg-[#3a0714] text-white shrink-0 h-screen sticky top-0 z-40 transition-all duration-300 ease-in-out overflow-hidden w-0 opacity-0 pointer-events-none">
        
        <div class="w-64 p-5 flex flex-col h-full justify-between overflow-y-auto">
            <div>
                <!-- Brand ASK UBAYA -->
                <div class="flex items-center gap-3 px-2 py-3 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo ASK UBAYA" class="w-10 h-10 rounded-xl shadow-md shrink-0">
                    <div>
                        <h1 class="text-base font-extrabold tracking-wide text-white leading-tight">ASK UBAYA</h1>
                        <p class="text-[10px] tracking-wider text-rose-200/60 font-semibold uppercase">STUDENT KNOWLEDGE</p>
                    </div>
                </div>

                <!-- User Profile Card di Sidebar -->
                <div class="bg-white/10 rounded-2xl p-3 flex items-center gap-3 border border-white/10 mb-6">
                    <div class="w-10 h-10 rounded-full bg-[#82142d] flex items-center justify-center font-bold text-white text-sm shadow-inner shrink-0">
                        {{ $initials }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ $userName }}</p>
                        <p class="text-[11px] text-rose-200/70 capitalize">{{ str_replace('_', ' ', $userRole) }}</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-white/40 px-3 mb-2">MENU UTAMA</p>

                    <!-- Validasi Konten (Active) -->
                    <a href="{{ route('validator.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-white/15 text-white shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-rose-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Validasi Konten</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Logout Button di Bagian Bawah Sidebar -->
            <div class="pt-4 border-t border-white/10">
                <a href="/logout" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-rose-200/80 hover:bg-white/10 hover:text-white transition">
                    <svg class="w-5 h-5 text-rose-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper: Otomatis menyesuaikan lebar (mengecil saat sidebar terbuka) -->
    <div id="mainWrapper" class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out">
        
        <!-- Top Navigation Bar -->
        <header class="bg-white border-b border-gray-100 sticky top-0 z-30 shadow-2xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Left Header: Tombol Hamburger di Kiri Pojok + Title & Subtitle -->
                <div class="flex items-center gap-3 sm:gap-3.5">
                    <!-- Tombol Toggle Sidebar (Menu Hamburger di Kiri Pojok) -->
                    <button type="button" onclick="toggleSidebar()" 
                            class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-[#FAF0F2] border border-gray-200 hover:border-[#F5D0D6] text-[#741228] flex items-center justify-center transition shadow-2xs cursor-pointer group shrink-0" 
                            title="Buka / Tutup Sidebar Menu">
                        <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                        </svg>
                    </button>

                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">@yield('page_title', 'Validasi Konten')</h1>
                        <p class="text-xs text-gray-400">
                            Validator &bull; {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                </div>

                <!-- Right Header: Notification & Profile Button (Trigger Toggle Sidebar) -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Notification Bell -->
                    <button type="button" class="relative p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition" title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- Profile Info Display (Static, Tidak memicu sidebar) -->
                    <div class="flex items-center gap-2.5 p-1 text-left">
                        <div class="w-9 h-9 rounded-full bg-[#FAF0F2] text-[#741228] font-bold text-xs flex items-center justify-center border border-[#F5D0D6] shadow-2xs">
                            {{ $initials }}
                        </div>
                        <div class="hidden sm:block leading-tight text-left pr-1">
                            <p class="text-xs font-bold text-gray-900">{{ $userName }}</p>
                            <p class="text-[11px] text-gray-400 capitalize">{{ str_replace('_', ' ', $userRole) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-1">
            @yield('content')
        </main>

    </div>

    <!-- Toggle Sidebar JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('leftSidebar');
            if (!sidebar) return;

            const isCollapsed = sidebar.classList.contains('w-0');

            if (isCollapsed) {
                sidebar.classList.remove('w-0', 'opacity-0', 'pointer-events-none');
                sidebar.classList.add('w-64', 'opacity-100', 'pointer-events-auto');
            } else {
                sidebar.classList.remove('w-64', 'opacity-100', 'pointer-events-auto');
                sidebar.classList.add('w-0', 'opacity-0', 'pointer-events-none');
            }
        }
    </script>

    @yield('scripts')
</body>
</html>


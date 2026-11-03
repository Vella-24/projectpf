<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin - ASK UBAYA')</title>

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
    $userName = session('user.name') ?? 'Super Admin UBAYA';
    $userRole = session('user.role') ?? 'Super Admin';
    $nameParts = explode(' ', trim($userName));
    $initials = '';
    if (count($nameParts) >= 2) {
        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
    } else {
        $initials = strtoupper(substr($userName, 0, 2));
    }
    if (!$initials) $initials = 'SA';
@endphp

    <!-- Sidebar Kiri Super Admin (Interaktif: Muncul saat profil diklik, tutup via silang/profil, konten mengecil/melebar) -->
    <aside id="leftSidebar" 
           class="bg-[#3a0714] text-white shrink-0 h-screen sticky top-0 z-40 transition-all duration-300 ease-in-out overflow-hidden w-0 opacity-0 pointer-events-none">
        
        <div class="w-64 p-5 flex flex-col h-full justify-between overflow-y-auto">
            <div>
                <!-- Brand ASK UBAYA -->
                <div class="flex items-center gap-3 px-2 py-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0 border border-white/10 shadow-sm">
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

                    <!-- Ringkasan (Placeholder) -->
                    <a href="javascript:void(0)" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-white/50 hover:text-white/80 hover:bg-white/5 transition cursor-default" title="Menu belum tersedia">
                        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Ringkasan</span>
                    </a>

                    <!-- Kelola Pengetahuan (Placeholder) -->
                    <a href="javascript:void(0)" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-white/50 hover:text-white/80 hover:bg-white/5 transition cursor-default" title="Menu belum tersedia">
                        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Kelola Pengetahuan</span>
                    </a>

                    <!-- Manajemen Pengguna (ACTIVE) -->
                    <a href="{{ route('superadmin.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('superadmin.*') ? 'bg-white/15 text-white shadow-sm' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 text-rose-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Manajemen Pengguna</span>
                    </a>

                    <!-- Kategori & Tag (Placeholder) -->
                    <a href="javascript:void(0)" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-white/50 hover:text-white/80 hover:bg-white/5 transition cursor-default" title="Menu belum tersedia">
                        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Kategori & Tag</span>
                    </a>

                    <!-- Analitik (Placeholder) -->
                    <a href="javascript:void(0)" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-white/50 hover:text-white/80 hover:bg-white/5 transition cursor-default" title="Menu belum tersedia">
                        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Analitik</span>
                    </a>

                    <!-- Pengaturan (Placeholder) -->
                    <a href="javascript:void(0)" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-white/50 hover:text-white/80 hover:bg-white/5 transition cursor-default" title="Menu belum tersedia">
                        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan</span>
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

    <!-- Content Wrapper: Otomatis mengecil / melebar saat sidebar dibuka/ditutup -->
    <div id="mainWrapper" class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out">
        
        <!-- Top Navigation Bar -->
        <header class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-2xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Left Title & Subtitle + Tombol Hamburger Kiri Pojok -->
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
                        <h1 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">@yield('page_title', 'Manajemen Pengguna')</h1>
                        <p class="text-xs text-gray-400">
                            Super Admin &bull; {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                </div>

                <!-- Right Notification & Profile (Trigger Toggle Sidebar) -->
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

        <!-- Flash Message Alert -->
        @if(session('sukses'))
        <div id="flashAlert" class="w-full px-4 sm:px-6 lg:px-8 mt-4">
            <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('sukses') }}</span>
                </div>
                <button onclick="document.getElementById('flashAlert').remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>
        </div>
        @endif

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


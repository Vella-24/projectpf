@extends('layouts.superadmin')

@section('title', 'Manajemen Pengguna - Super Admin ASK UBAYA')
@section('page_title', 'Manajemen Pengguna')

@section('content')
    <!-- Page Header & Tambah Data Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h2>
            <p class="text-sm text-gray-500 mt-1">
                Kelola data manajemen pengguna dengan cepat dan terstruktur.
            </p>
        </div>
        <div>
            <!-- Tombol Tambah Data -->
            <a href="{{ route('superadmin.users.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-[#741228] hover:bg-[#5e0d1f] text-white shadow-sm transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Data</span>
            </a>
        </div>
    </div>

    <!-- Table Card Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-6">
        
        <!-- Controls: Search, Role Filter, Reset -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" oninput="filterUsers()"
                       placeholder="Cari data..."
                       class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition">
            </div>

            <!-- Filter Dropdown & Reset Button -->
            <div class="flex items-center gap-2.5 self-end sm:self-auto">
                <div class="relative">
                    <select id="roleFilter" onchange="filterUsers()"
                            class="appearance-none bg-gray-50/70 border border-gray-200 rounded-xl py-2.5 pl-3.5 pr-8 text-sm text-gray-700 font-medium focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition cursor-pointer">
                        <option value="semua">Semua role</option>
                        <option value="data_entry">Data Entry</option>
                        <option value="validator">Validator</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>

                <!-- Reset Button -->
                <button type="button" onclick="resetFilter()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                    Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                        <th class="py-3 px-4 rounded-l-xl">NAMA</th>
                        <th class="py-3 px-4">ROLE</th>
                        <th class="py-3 px-4 text-right rounded-r-xl">AKSI</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="user-row hover:bg-gray-50/60 transition group"
                        data-name="{{ strtolower($user->name) }}"
                        data-email="{{ strtolower($user->email) }}"
                        data-role="{{ strtolower($user->role) }}">
                        
                        <!-- Nama & Email di bawahnya -->
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-gray-900 text-sm leading-snug">{{ $user->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</div>
                        </td>

                        <!-- Role Pengguna -->
                        <td class="py-3.5 px-4 text-sm text-gray-600 font-medium">
                            {{ ucwords(str_replace('_', ' ', $user->role)) }}
                        </td>

                        <!-- Aksi (Edit & Delete Icons Sesuai Desain Foto) -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="inline-flex items-center gap-1 justify-end">
                                <!-- Edit Icon -->
                                <a href="{{ route('superadmin.users.edit', $user->id) }}" 
                                   class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" 
                                   title="Edit Pengguna">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>

                                <!-- Delete Icon -->
                                <form action="{{ route('superadmin.users.destroy', $user->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah kamu yakin ingin menghapus pengguna ini?')" 
                                      class="inline-block m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" 
                                            title="Hapus Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyTableRow">
                        <td colspan="3" class="py-8 text-center text-gray-400 text-sm">
                            Belum ada data pengguna.
                        </td>
                    </tr>
                    @endforelse

                    <!-- Baris jika filter/search tidak menemukan hasil -->
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="3" class="py-8 text-center text-gray-400 text-sm">
                            Tidak ada data pengguna yang sesuai dengan pencarian atau filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    function filterUsers() {
        const keyword = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
        const roleFilter = document.getElementById('roleFilter')?.value || 'semua';
        const rows = document.querySelectorAll('.user-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const role = row.dataset.role || '';

            const matchKeyword = !keyword || name.includes(keyword) || email.includes(keyword);
            const matchRole = (roleFilter === 'semua') || (role === roleFilter);

            if (matchKeyword && matchRole) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) {
            if (visibleCount === 0 && rows.length > 0) {
                noResultsRow.classList.remove('hidden');
            } else {
                noResultsRow.classList.add('hidden');
            }
        }
    }

    function resetFilter() {
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        if (searchInput) searchInput.value = '';
        if (roleFilter) roleFilter.value = 'semua';
        filterUsers();
    }
</script>
@endsection

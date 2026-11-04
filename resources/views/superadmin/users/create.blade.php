@extends('layouts.superadmin')

@section('title', 'Tambah Pengguna - Super Admin ASK UBAYA')
@section('page_title', 'Tambah Pengguna')

@section('content')
    <div class="max-w-xl mx-auto">
        <!-- Back Link -->
        <div class="mb-5">
            <a href="{{ route('superadmin.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#741228] hover:text-[#5e0d1f] transition">
                &larr; Kembali ke Manajemen Pengguna
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Tambah Pengguna</h2>
                <p class="text-sm text-gray-500 mt-1">Masukkan informasi akun pengguna baru ke dalam sistem.</p>
            </div>

            <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-800 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required
                           class="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-800 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@ubaya.ac.id" required
                           class="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-gray-800 mb-1.5">Password</label>
                    <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required
                           class="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-800 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" required
                           class="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition">
                </div>

                <!-- Role Pengguna -->
                <div>
                    <label for="role" class="block text-sm font-bold text-gray-800 mb-1.5">Role Pengguna</label>
                    <select name="role" id="role" required
                            class="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-800 focus:bg-white focus:outline-none focus:border-[#741228] focus:ring-2 focus:ring-[#741228]/15 transition cursor-pointer">
                        <option value="">-- Pilih Role --</option>
                        <option value="data_entry" {{ old('role') == 'data_entry' ? 'selected' : '' }}>Data Entry</option>
                        <option value="validator" {{ old('role') == 'validator' ? 'selected' : '' }}>Validator</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('superadmin.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm bg-[#741228] hover:bg-[#5e0d1f] text-white shadow-sm transition cursor-pointer">
                        Simpan Pengguna
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

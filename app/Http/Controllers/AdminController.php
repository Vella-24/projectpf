<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $users = User::all();

        return view('superadmin.index', compact('users'));
    }

    // Menampilkan form tambah pengguna
    public function create()
    {
        return view('superadmin.create');
    }

    // Menyimpan pengguna baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:data_entry,validator,super_admin',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('superadmin.index')
            ->with('sukses', 'Pengguna berhasil ditambahkan!');
    }
    // Menampilkan form edit
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('superadmin.edit', compact('user'));
    }

    // Memperbarui pengguna
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:data_entry,validator,super_admin',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        // Password hanya diperbarui jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('superadmin.index')
            ->with('sukses', 'Pengguna berhasil diperbarui!');
    }

    // Menghapus pengguna
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('superadmin.index')
            ->with('sukses', 'Pengguna berhasil dihapus!');
    }
}

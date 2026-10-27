<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Menampilkan daftar seluruh kategori pertanyaan
     */
    public function index()
    {
        if (session('user.role') === 'data_entry') {
            return redirect()->route('pertanyaan.index')->with('error', 'Akses ditolak: Akun Data Entry tidak diizinkan mengelola kategori.');
        }
        $kategoris = Kategori::withCount('pertanyaans')->orderBy('nama_kategori')->get();
        return view('kategori.index', compact('kategoris'));
    }

    /**
     * Menampilkan formulir tambah kategori
     */
    public function create()
    {
        if (session('user.role') === 'data_entry') {
            return redirect()->route('pertanyaan.index')->with('error', 'Akses ditolak: Akun Data Entry tidak diizinkan mengelola kategori.');
        }
        return view('kategori.create');
    }

    /**
     * Menyimpan kategori baru ke database
     */
    public function store(Request $request)
    {
        if (session('user.role') === 'data_entry') {
            return redirect()->route('pertanyaan.index')->with('error', 'Akses ditolak: Akun Data Entry tidak diizinkan mengelola kategori.');
        }
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:150|unique:kategoris,nama_kategori',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
        ]);

        Kategori::create($validated);

        return redirect()->route('kategori.index')->with('sukses', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit kategori
     */
    public function edit($id)
    {
        if (session('user.role') === 'data_entry') {
            return redirect()->route('pertanyaan.index')->with('error', 'Akses ditolak: Akun Data Entry tidak diizinkan mengelola kategori.');
        }
        $kategori = Kategori::findOrFail($id);
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Memperbarui kategori
     */
    public function update(Request $request, $id)
    {
        if (session('user.role') === 'data_entry') {
            return redirect()->route('pertanyaan.index')->with('error', 'Akses ditolak: Akun Data Entry tidak diizinkan mengelola kategori.');
        }
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:150|unique:kategoris,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')->with('sukses', 'Kategori berhasil diperbarui!');
    }

    /**
     * Menghapus kategori
     */
    public function destroy($id)
    {
        if (session('user.role') === 'data_entry') {
            return redirect()->route('pertanyaan.index')->with('error', 'Akses ditolak: Akun Data Entry tidak diizinkan mengelola kategori.');
        }
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->route('kategori.index')->with('sukses', 'Kategori berhasil dihapus!');
    }
}

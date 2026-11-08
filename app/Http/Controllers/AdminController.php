<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\Kategori;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    // ==========================================
    // 4. KELOLA PERTANYAAN (SUPERADMIN)
    // ==========================================
    public function pertanyaanIndex(Request $request)
    {
        // Hanya tampilkan pertanyaan yang sudah divalidasi oleh validator (status = disetujui)
        $query = Pertanyaan::with(['kategori', 'user'])
            ->where('status', 'disetujui')
            ->latest();

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('q')) {
            $searchTerm = trim($request->q);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('judul_pertanyaan', 'like', "%{$searchTerm}%")
                  ->orWhere('isi_jawaban', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function ($uq) use ($searchTerm) {
                      $uq->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                      $tagQuery->where('nama_tag', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $pertanyaans = $query->paginate(10)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('superadmin.pertanyaan.index', compact('pertanyaans', 'kategoris'));
    }

    public function pertanyaanCreate()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $tags = Tag::orderBy('nama_tag')->get();

        return view('superadmin.pertanyaan.create', compact('kategoris', 'tags'));
    }

    public function pertanyaanStore(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'status' => 'required|in:disetujui,draft',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ], [
            'kategori_id.required' => 'Kategori pertanyaan wajib dipilih.',
            'judul_pertanyaan.required' => 'Judul pertanyaan wajib diisi.',
            'isi_jawaban.required' => 'Isi jawaban wajib diisi.',
            'status.required' => 'Status pertanyaan wajib ditentukan.',
        ]);

        $daftarTag = $validated['tags'] ?? [];
        unset($validated['tags']);

        $validated['user_id'] = session('user.id') ?? User::where('role', 'super_admin')->value('id') ?? 1;

        $pertanyaan = Pertanyaan::create($validated);

        $idTags = [];
        foreach ($daftarTag as $namaTag) {
            $namaTag = strtolower(trim($namaTag));
            if ($namaTag === '') continue;

            $tag = Tag::firstOrCreate(['nama_tag' => $namaTag]);
            $idTags[] = $tag->id;
        }

        $pertanyaan->tags()->sync(array_unique($idTags));

        $pesan = $validated['status'] === 'disetujui'
            ? 'Pertanyaan baru berhasil dibuat dan langsung dipublikasikan ke halaman mahasiswa!'
            : 'Pertanyaan baru berhasil disimpan sebagai draf.';

        return redirect()->route('superadmin.pertanyaan.index')->with('sukses', $pesan);
    }

    public function pertanyaanEdit($id)
    {
        $pertanyaan = Pertanyaan::with(['tags', 'kategori'])->findOrFail($id);
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $tags = Tag::orderBy('nama_tag')->get();

        return view('superadmin.pertanyaan.edit', compact('pertanyaan', 'kategoris', 'tags'));
    }

    public function pertanyaanUpdate(Request $request, $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'status' => 'required|in:draft,menunggu_validasi,disetujui,ditolak',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ]);

        $daftarTag = $validated['tags'] ?? [];
        unset($validated['tags']);

        $tagLama = $pertanyaan->tags()->pluck('tags.id')->toArray();

        $pertanyaan->update($validated);

        $idTags = [];
        foreach ($daftarTag as $namaTag) {
            $namaTag = strtolower(trim($namaTag));
            if ($namaTag === '') continue;

            $tag = Tag::firstOrCreate(['nama_tag' => $namaTag]);
            $idTags[] = $tag->id;
        }

        $idTags = array_unique($idTags);
        $pertanyaan->tags()->sync($idTags);

        // Hapus tag yang tidak lagi digunakan
        $tagDihapus = array_diff($tagLama, $idTags);
        foreach ($tagDihapus as $idTag) {
            $tag = Tag::find($idTag);
            if ($tag && $tag->pertanyaans()->count() === 0) {
                $tag->delete();
            }
        }

        return redirect()->route('superadmin.pertanyaan.index')->with('sukses', 'Pertanyaan berhasil diperbarui!');
    }

    public function pertanyaanDestroy($id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $idTags = $pertanyaan->tags()->pluck('tags.id')->toArray();

        $pertanyaan->delete();

        foreach ($idTags as $idTag) {
            $tag = Tag::find($idTag);
            if ($tag && $tag->pertanyaans()->count() === 0) {
                $tag->delete();
            }
        }

        return redirect()->route('superadmin.pertanyaan.index')->with('sukses', 'Pertanyaan berhasil dihapus!');
    }

    // ==========================================
    // 5. MANAJEMEN PENGGUNA
    // ==========================================
    // Menampilkan daftar pengguna
    public function index()
    {
        $users = User::all();

        return view('superadmin.users.index', compact('users'));
    }

    // Menampilkan form tambah pengguna
    public function create()
    {
        return view('superadmin.users.create');
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

        return view('superadmin.users.edit', compact('user'));
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
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

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

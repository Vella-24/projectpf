<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pertanyaan;
use App\Models\Tag;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    /**
     * Menampilkan daftar seluruh pertanyaan (FAQ) untuk Data Entry
     */
    public function index(Request $request)
    {
        $query = Pertanyaan::with(['kategori', 'user'])->latest();

        // Filter berdasarkan kategori jika dipilih
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter berdasarkan status jika dipilih
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian kata kunci pada judul pertanyaan atau jawaban
        if ($request->filled('q')) {
            $searchTerm = $request->q;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('judul_pertanyaan', 'like', "%{$searchTerm}%")
                ->orWhere('isi_jawaban', 'like', "%{$searchTerm}%")
                ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                    $tagQuery->where('nama_tag', 'like', "%{$searchTerm}%");
                });
            });
        }

        $pertanyaans = $query->paginate(10)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pertanyaan.index', compact('pertanyaans', 'kategoris'));
    }

    /**
     * Menampilkan formulir tambah pertanyaan baru
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $tags = Tag::orderBy('nama_tag')->get();

        return view('pertanyaan.create', compact('kategoris', 'tags'));
    }

    /**
     * Menyimpan data pertanyaan baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'status' => 'required|in:draft,menunggu_validasi,disetujui,ditolak',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ], [
            'kategori_id.required' => 'Kategori pertanyaan wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'judul_pertanyaan.required' => 'Judul pertanyaan wajib diisi.',
            'isi_jawaban.required' => 'Isi jawaban resmi wajib diisi.',
            'status.required' => 'Status pertanyaan wajib dipilih.',
        ]);

        $daftarTag = $validated['tags'] ?? []; //ambil daftar kata kunci
        unset($validated['tags']);

        // Pada Alpha version, default user_id menggunakan akun data entry (ID 1)
        $validated['user_id'] = 1;

        $pertanyaan = Pertanyaan::create($validated);

        $idTags = [];

        foreach ($daftarTag as $namaTag) {
            $namaTag = strtolower(trim($namaTag));

                if ($namaTag === '') {
                    continue;
                }

            $tag = Tag::firstOrCreate([
                'nama_tag' => $namaTag,
            ]);

            $idTags[] = $tag->id;
        }
        $pertanyaan->tags()->sync(array_unique($idTags)); //simpen relasi ke tble pertanyaan_tag

        return redirect()->route('pertanyaan.index')->with('sukses', 'Pertanyaan baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail pertanyaan
     */
    public function show($id)
    {
        $pertanyaan = Pertanyaan::with(['kategori', 'user'])->findOrFail($id);
        return view('pertanyaan.show', compact('pertanyaan'));
    }

    /**
     * Menampilkan formulir ubah pertanyaan
     */
    public function edit($id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $tags = Tag::orderBy('nama_tag')->get();
        $pertanyaan->load('tags');


        return view('pertanyaan.edit', compact('pertanyaan', 'kategoris', 'tags'));
    }

    /**
     * Memperbarui data pertanyaan di database
     */
    public function update(Request $request, $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'status' => 'required|in:draft,menunggu_validasi,disetujui,ditolak',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ], [
            'kategori_id.required' => 'Kategori pertanyaan wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'judul_pertanyaan.required' => 'Judul pertanyaan wajib diisi.',
            'isi_jawaban.required' => 'Isi jawaban resmi wajib diisi.',
            'status.required' => 'Status pertanyaan wajib dipilih.',
        ]);

        $daftarTag = $validated['tags'] ?? [];
        unset($validated['tags']);

        $tagLama = $pertanyaan->tags()->pluck('tags.id')->toArray();

        $pertanyaan->update($validated);
       
        $idTags = [];

        foreach ($daftarTag as $namaTag) {
            $namaTag = strtolower(trim($namaTag));

            if ($namaTag === '') {
                continue;
            }

            $tag = Tag::firstOrCreate([
                'nama_tag' => $namaTag,
            ]);

            $idTags[] = $tag->id;
        }

        $idTags = array_unique($idTags);

        //ini bwat hubungkan pertanyaan dengan tag yang dipilih
        $pertanyaan->tags()->sync($idTags);

        //ini bwat klo cari tag lama yang sudah dilepas dari pertanyaan ini
        $tagDihapus = array_diff($tagLama, $idTags);

        foreach ($tagDihapus as $idTag) {
            $tag = Tag::find($idTag);

            //ini hapus tag dari tabel tags hanya jika tidak dipakai pertanyaan lain
            if ($tag && $tag->pertanyaans()->count() === 0) {
                $tag->delete();
            }
        }

        return redirect()->route('pertanyaan.index')->with('sukses', 'Data pertanyaan berhasil diperbarui!');
    }

    /**
     * Menghapus pertanyaan dari database
     */
    public function destroy($id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $pertanyaan->delete();

        return redirect()->route('pertanyaan.index')->with('sukses', 'Data pertanyaan berhasil dihapus!');
    }

    public function indexMahasiswa(Request $request)
    {
        $query = Pertanyaan::with(['kategori', 'user'])->latest();

        // Filter berdasarkan kategori jika dipilih
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter berdasarkan status jika dipilih
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian kata kunci pada judul pertanyaan atau jawaban
        if ($request->filled('q')) {
            $searchTerm = $request->q;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('judul_pertanyaan', 'like', "%{$searchTerm}%")
                ->orWhere('isi_jawaban', 'like', "%{$searchTerm}%")
                ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                    $tagQuery->where('nama_tag', 'like', "%{$searchTerm}%");
                });
            });
        }

        $pertanyaans = $query->paginate(10)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('mahasiswa.index', compact('pertanyaans', 'kategoris'));
    }
}

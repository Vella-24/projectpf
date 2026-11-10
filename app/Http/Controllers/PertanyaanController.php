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
        if (session('user.role') !== 'data_entry') {
            abort(403);
        }

        $query = Pertanyaan::with(['kategori', 'user'])
        ->where('user_id', session('user.id'))
        ->latest();

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

        $userId = session('user.id');
        $queryStat = Pertanyaan::where('user_id', $userId);

        $totalSemua     = (clone $queryStat)->count();
        $totalMenunggu  = (clone $queryStat)->where('status', 'menunggu_validasi')->count();
        $totalDisetujui = (clone $queryStat)->where('status', 'disetujui')->count();
        $totalDitolak   = (clone $queryStat)->where('status', 'ditolak')->count();

        return view('pertanyaan.index', compact(
            'pertanyaans',
            'kategoris',
            'totalSemua',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak'
        ));
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
            'status' => 'required|in:draft,menunggu_validasi',
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

        //ambil id user yg sedang login
        $validated['user_id'] = session('user.id');

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
        $pertanyaan->searchable();

        $pesan = $validated['status'] === 'draft'
        ? 'Pertanyaan berhasil disimpan sebagai draft.'
        : 'Pertanyaan berhasil dikirim dan menunggu validasi.';

        return redirect()->route('pertanyaan.index')->with('sukses', $pesan);
    }

    /**
     * Menampilkan detail pertanyaan
     */
    public function show($id)
    {
        $pertanyaan = Pertanyaan::with(['kategori', 'user'])
            ->where('user_id', session('user.id'))
            ->findOrFail($id);

        return view('pertanyaan.show', compact('pertanyaan'));
    }

    /**
     * Menampilkan formulir ubah pertanyaan
     */
    public function edit($id)
    {
        $pertanyaan = Pertanyaan::where('user_id', session('user.id'))->findOrFail($id);
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
        $pertanyaan = Pertanyaan::where('user_id', session('user.id'))->findOrFail($id);

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'status' => 'required|in:draft,menunggu_validasi',
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
        $pertanyaan->searchable();

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
        $pertanyaan = Pertanyaan::where('user_id', session('user.id'))->findOrFail($id);

        //simpen id tag yg digunakan pertanyaan ini
        $idTags = $pertanyaan->tags()->pluck('tags.id')->toArray();

        $pertanyaan->delete();

        //periksa tag 1"
        foreach ($idTags as $idTag) {
            $tag = Tag::find($idTag);

            //hapus tag jika sudah tidak digunakan pertanyaan mana pun
            if ($tag && $tag->pertanyaans()->count() === 0) {
                $tag->delete();
            }
        }


        return redirect()->route('pertanyaan.index')->with('sukses', 'Data pertanyaan berhasil dihapus!');
    }

    public function indexMahasiswa(Request $request)
    {
        $kategoris = Kategori::withCount(['pertanyaans' => function ($q) {
            $q->where('status', 'disetujui');
        }])->orderBy('nama_kategori')->get();

        // Jika terdapat pencarian kata kunci, gunakan Meilisearch melalui Laravel Scout
        if ($request->filled('q')) {
            $searchTerm = trim($request->q);

            try {
                $scoutQuery = Pertanyaan::search($searchTerm);

                if ($request->filled('kategori_id')) {
                    $scoutQuery->where('kategori_id', (int) $request->kategori_id);
                }

                $pertanyaans = $scoutQuery->query(function ($query) {
                    $query->with(['kategori', 'user', 'tags'])
                        ->where('status', 'disetujui');
                })->paginate(10)->withQueryString();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Meilisearch search error, falling back to database: ' . $e->getMessage());

                $query = Pertanyaan::with(['kategori', 'user', 'tags'])->latest();
                $query->where('status', 'disetujui');

                if ($request->filled('kategori_id')) {
                    $query->where('kategori_id', $request->kategori_id);
                }

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('judul_pertanyaan', 'like', "%{$searchTerm}%")
                    ->orWhere('isi_jawaban', 'like', "%{$searchTerm}%")
                    ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                        $tagQuery->where('nama_tag', 'like', "%{$searchTerm}%");
                    });
                });

                $pertanyaans = $query->paginate(10)->withQueryString();
            }
        } else {
            $query = Pertanyaan::with(['kategori', 'user', 'tags'])->latest();
            $query->where('status', 'disetujui');

            if ($request->filled('kategori_id')) {
                $query->where('kategori_id', $request->kategori_id);
            }

            $pertanyaans = $query->paginate(10)->withQueryString();
        }

        return view('mahasiswa.index', compact('pertanyaans', 'kategoris'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:255',
        ]);

        $keyword = trim($request->q);

        try {
            $hasil = Pertanyaan::search($keyword)->query(function ($query) {
                $query->with(['kategori', 'user', 'tags'])->where('status', 'disetujui');
            })->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Meilisearch search error: ' . $e->getMessage());
            $hasil = Pertanyaan::with(['kategori', 'user', 'tags'])
                ->where('status', 'disetujui')
                ->where(function ($q) use ($keyword) {
                    $q->where('judul_pertanyaan', 'like', "%{$keyword}%")
                    ->orWhere('isi_jawaban', 'like', "%{$keyword}%");
                })->get();
        }

        return view('mahasiswa.search', compact('hasil', 'keyword'));
    }
}

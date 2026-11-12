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

        // Cek kemiripan pertanyaan di server-side (mencegah bypass)
        $kemiripan = $this->temukanPertanyaanMirip($validated['judul_pertanyaan']);
        if ($kemiripan) {
            return back()->withInput()->withErrors([
                'judul_pertanyaan' => "Pertanyaan tidak dapat disimpan karena terdeteksi serupa ({$kemiripan['kemiripan']}%) dengan pertanyaan yang sudah ada: \"{$kemiripan['judul_pertanyaan']}\". Silakan ubah teks pertanyaan agar tidak duplikat."
            ]);
        }

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

        // Cek kemiripan pertanyaan di server-side (mencegah bypass), kecualikan pertanyaan saat ini
        $kemiripan = $this->temukanPertanyaanMirip($validated['judul_pertanyaan'], $pertanyaan->id);
        if ($kemiripan) {
            return back()->withInput()->withErrors([
                'judul_pertanyaan' => "Pertanyaan tidak dapat disimpan karena terdeteksi serupa ({$kemiripan['kemiripan']}%) dengan pertanyaan yang sudah ada: \"{$kemiripan['judul_pertanyaan']}\". Silakan ubah teks pertanyaan agar tidak duplikat."
            ]);
        }

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

    /**
     * Memeriksa kemiripan pertanyaan baru dengan pertanyaan yang sudah ada
     * menggunakan algoritma Fuzzy Matching (Levenshtein Distance).
     */
    public function cekKemiripan(Request $request)
    {
        if (session('user.role') !== 'data_entry') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inputPertanyaan = trim((string) $request->input('pertanyaan', ''));

        // Jika teks terlalu pendek, tidak perlu diperiksa
        if (mb_strlen($inputPertanyaan, 'UTF-8') < 5) {
            return response()->json(['matches' => []]);
        }

        $cleanInput = $this->normalisasiTeks($inputPertanyaan);
        if (empty($cleanInput)) {
            return response()->json(['matches' => []]);
        }

        // Ambil semua pertanyaan yang ada di database (seluruh user/data entry)
        $query = Pertanyaan::with(['kategori', 'user'])
            ->select('id', 'judul_pertanyaan', 'isi_jawaban', 'kategori_id', 'user_id', 'status');

        // Jika dalam mode edit, abaikan pertanyaan itu sendiri
        if ($request->filled('ignore_id')) {
            $query->where('id', '!=', $request->ignore_id);
        }

        $pertanyaans = $query->get();
        $matches = [];

        // Batas ambang kemiripan minimum (default 70%)
        $threshold = (float) $request->input('threshold', 70);

        foreach ($pertanyaans as $item) {
            $cleanTarget = $this->normalisasiTeks($item->judul_pertanyaan);
            if (empty($cleanTarget)) {
                continue;
            }

            // Batasi string maksimal 255 karakter sesuai batasan fungsi levenshtein() PHP
            $str1 = mb_substr($cleanInput, 0, 255, 'UTF-8');
            $str2 = mb_substr($cleanTarget, 0, 255, 'UTF-8');

            $len1 = strlen($str1);
            $len2 = strlen($str2);
            $maxLen = max($len1, $len2);

            if ($maxLen === 0) {
                continue;
            }

            $dist = levenshtein($str1, $str2);
            $levScore = (1 - ($dist / $maxLen)) * 100;

            // similar_text sebagai pelengkap token/panjang teks
            similar_text($str1, $str2, $simScore);

            $finalScore = max($levScore, $simScore);

            if ($finalScore >= $threshold) {
                // Tentukan label status yang mudah dipahami
                $statusLabel = match ($item->status) {
                    'disetujui' => 'Dipublikasikan',
                    'menunggu_validasi' => 'Menunggu Validasi',
                    'draft' => 'Draft',
                    'ditolak' => 'Perlu Revisi',
                    default => ucfirst($item->status),
                };

                $matches[] = [
                    'id' => $item->id,
                    'judul_pertanyaan' => $item->judul_pertanyaan,
                    'isi_jawaban' => \Illuminate\Support\Str::limit(strip_tags($item->isi_jawaban), 160),
                    'kategori' => $item->kategori->nama_kategori ?? 'Umum',
                    'pembuat' => $item->user->name ?? 'Staff Data Entry',
                    'status' => $item->status,
                    'status_label' => $statusLabel,
                    'kemiripan' => (int) round($finalScore),
                ];
            }
        }

        // Urutkan dari tingkat kemiripan tertinggi ke terendah
        usort($matches, function ($a, $b) {
            return $b['kemiripan'] <=> $a['kemiripan'];
        });

        // Ambil maksimal 5 pertanyaan paling mirip
        $topMatches = array_slice($matches, 0, 5);

        return response()->json([
            'query' => $inputPertanyaan,
            'matches' => $topMatches,
            'total_found' => count($matches),
        ]);
    }

    /**
     * Mencari pertanyaan yang paling mirip di database jika melewati ambang batas kemiripan (threshold 70%).
     */
    protected function temukanPertanyaanMirip(string $judul, ?int $ignoreId = null, float $threshold = 70.0): ?array
    {
        $cleanInput = $this->normalisasiTeks($judul);
        if (mb_strlen($cleanInput, 'UTF-8') < 5) {
            return null;
        }

        $query = Pertanyaan::select('id', 'judul_pertanyaan');
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $pertanyaans = $query->get();
        $topMatch = null;
        $highestScore = 0;

        foreach ($pertanyaans as $item) {
            $cleanTarget = $this->normalisasiTeks($item->judul_pertanyaan);
            if (empty($cleanTarget)) {
                continue;
            }

            $str1 = mb_substr($cleanInput, 0, 255, 'UTF-8');
            $str2 = mb_substr($cleanTarget, 0, 255, 'UTF-8');

            $len1 = strlen($str1);
            $len2 = strlen($str2);
            $maxLen = max($len1, $len2);

            if ($maxLen === 0) {
                continue;
            }

            $dist = levenshtein($str1, $str2);
            $levScore = (1 - ($dist / $maxLen)) * 100;

            similar_text($str1, $str2, $simScore);
            $score = max($levScore, $simScore);

            if ($score >= $threshold && $score > $highestScore) {
                $highestScore = $score;
                $topMatch = [
                    'id' => $item->id,
                    'judul_pertanyaan' => $item->judul_pertanyaan,
                    'kemiripan' => (int) round($score),
                ];
            }
        }

        return $topMatch;
    }

    /**
     * Normalisasi teks untuk perbandingan fuzzy (lowercase, hapus tanda baca & spasi ganda)
     */
    protected function normalisasiTeks(string $teks): string
    {
        $teks = mb_strtolower($teks, 'UTF-8');
        // Ganti karakter non huruf/angka dengan spasi
        $teks = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $teks);
        // Hapus spasi ganda
        $teks = preg_replace('/\s+/', ' ', $teks);
        return trim($teks);
    }
}

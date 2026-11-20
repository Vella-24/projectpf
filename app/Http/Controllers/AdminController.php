<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\Kategori;
use App\Models\Tag;
use App\Models\RiwayatJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    // ==========================================
    // KELOLA PERTANYAAN (SUPERADMIN)
    // ==========================================
    public function pertanyaanIndex(Request $request)
    {
        $userId = session('user.id');

        // Hanya: (a) semua yang disetujui, (b) draf milik super admin ini
        $query = Pertanyaan::with(['kategori', 'user', 'tags'])
            ->where(function ($q) use ($userId) {
                $q->where('status', Pertanyaan::DISETUJUI)
                ->orWhere(function ($q2) use ($userId) {
                    $q2->where('status', Pertanyaan::DRAFT)
                        ->where('user_id', $userId);
                });
            })
            ->latest();

        if ($request->filled('status')) {
            $normalizedStatus = Pertanyaan::normalizeStatus($request->status);
            if ($normalizedStatus !== null) {
                $query->where('status', $normalizedStatus);
            }
        }

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
        $daftarFakultas = \App\Helpers\FakultasHelper::getAll();
        $daftarFakultas = \App\Helpers\FakultasHelper::getAll();

        return view('superadmin.pertanyaan.create', compact('kategoris', 'tags', 'daftarFakultas'));
    }

    public function pertanyaanStore(Request $request)
    {
        $statusInput = $request->input('status');
        $normalizedStatus = Pertanyaan::normalizeStatus($statusInput);
        if ($normalizedStatus !== null && in_array($normalizedStatus, [Pertanyaan::DRAFT, Pertanyaan::DISETUJUI], true)) {
            $request->merge(['status' => $normalizedStatus]);
        }

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'fakultas' => 'nullable|string|in:umum,11,12,13,14,15,16,17,18,19',
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'status' => 'required|integer|in:0,2',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ], [
            'kategori_id.required' => 'Kategori pertanyaan wajib dipilih.',
            'judul_pertanyaan.required' => 'Judul pertanyaan wajib diisi.',
            'isi_jawaban.required' => 'Isi jawaban wajib diisi.',
            'status.required' => 'Status pertanyaan wajib ditentukan.',
        ]);

        $validated['fakultas'] = $validated['fakultas'] ?? 'umum';

        // Cek kemiripan pertanyaan di server-side (mencegah duplikat bypass)
        $kemiripan = $this->temukanPertanyaanMirip($validated['judul_pertanyaan']);
        if ($kemiripan) {
            return back()->withInput()->withErrors([
                'judul_pertanyaan' => "Pertanyaan tidak dapat disimpan karena terdeteksi serupa ({$kemiripan['kemiripan']}%) dengan pertanyaan yang sudah ada: \"{$kemiripan['judul_pertanyaan']}\". Silakan ubah teks pertanyaan agar tidak duplikat."
            ]);
        }

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
        try {
            $pertanyaan->searchable();
        } catch (\Throwable $e) {
            // Meilisearch offline, simpan ke database tetap berhasil
        }

        $pesan = (int) $validated['status'] === Pertanyaan::DISETUJUI
            ? 'Pertanyaan baru berhasil dibuat dan langsung dipublikasikan ke halaman mahasiswa!'
            : 'Pertanyaan baru berhasil disimpan sebagai draf.';

        return redirect()->route('superadmin.pertanyaan.index')->with('sukses', $pesan);
    }

    public function pertanyaanEdit($id)
    {
        $pertanyaan = Pertanyaan::with(['tags', 'kategori'])->findOrFail($id);
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $tags = Tag::orderBy('nama_tag')->get();
        $daftarFakultas = \App\Helpers\FakultasHelper::getAll();

        return view('superadmin.pertanyaan.edit', compact('pertanyaan', 'kategoris', 'tags', 'daftarFakultas'));
    }

    public function pertanyaanUpdate(Request $request, $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);

        $statusInput = $request->input('status');
        $normalizedStatus = Pertanyaan::normalizeStatus($statusInput);
        if ($normalizedStatus !== null) {
            $request->merge(['status' => $normalizedStatus]);
        }

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'fakultas' => 'nullable|string|in:umum,11,12,13,14,15,16,17,18,19',
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'status' => 'required|integer|in:0,1,2,3',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ]);

        $validated['fakultas'] = $validated['fakultas'] ?? 'umum';

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
        $jawabanLama = $pertanyaan->isi_jawaban;

        $pertanyaan->update($validated);

        if ($jawabanLama !== $validated['isi_jawaban']) {
            RiwayatJawaban::create([
                'pertanyaan_id' => $pertanyaan->id,
                'jawaban_lama' => $jawabanLama,
                'jawaban_baru' => $validated['isi_jawaban'],
                'user_id' => session('user.id') ?? auth()->id(),
            ]);
        }

        $idTags = [];
        foreach ($daftarTag as $namaTag) {
            $namaTag = strtolower(trim($namaTag));
            if ($namaTag === '') continue;

            $tag = Tag::firstOrCreate(['nama_tag' => $namaTag]);
            $idTags[] = $tag->id;
        }

        $idTags = array_unique($idTags);
        $pertanyaan->tags()->sync($idTags);
        try {
            $pertanyaan->searchable();
        } catch (\Throwable $e) {
            // Meilisearch offline
        }

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

    /**
     * Memeriksa kemiripan pertanyaan baru dengan pertanyaan yang sudah ada
     * menggunakan algoritma Fuzzy Matching (Levenshtein Distance & Similar Text).
     */
    public function cekKemiripan(Request $request)
    {
        $inputPertanyaan = trim((string) $request->input('pertanyaan', ''));

        // Jika teks terlalu pendek, tidak perlu diperiksa
        if (mb_strlen($inputPertanyaan, 'UTF-8') < 5) {
            return response()->json(['matches' => []]);
        }

        $cleanInput = $this->normalisasiTeks($inputPertanyaan);
        if (empty($cleanInput)) {
            return response()->json(['matches' => []]);
        }

        // Ambil semua pertanyaan yang ada di database
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
                $statusLabel = match ((int) $item->status) {
                    2 => 'Dipublikasikan',
                    1 => 'Menunggu Validasi',
                    0 => 'Draft',
                    3 => 'Perlu Revisi',
                    default => 'Tidak Diketahui',
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
    // MANAJEMEN PENGGUNA
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

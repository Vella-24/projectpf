<?php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use App\Models\PertanyaanLanjutan;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PertanyaanLanjutanController extends Controller
{
    // Menampilkan halaman pertanyaan lanjutan
    public function create($id)
    {
        $pertanyaan = Pertanyaan::where('status', 'disetujui')
            ->findOrFail($id);

        return view(
            'mahasiswa.pertanyaan-lanjutan',
            compact('pertanyaan')
        );
    }

    // Menyimpan pertanyaan lanjutan mahasiswa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan_id' => 'required|exists:pertanyaans,id',
            'email_mahasiswa' => 'required|email|max:150',
            'isi_pertanyaan' => 'required|string|max:2000',
        ]);

        // Pastikan FAQ masih disetujui
        $faq = Pertanyaan::where('status', 'disetujui')
            ->findOrFail($validated['pertanyaan_id']);

        PertanyaanLanjutan::create([
            'pertanyaan_id' => $faq->id,
            'email_mahasiswa' => $validated['email_mahasiswa'],
            'isi_pertanyaan' => $validated['isi_pertanyaan'],
            'status' => 'baru',
        ]);

        return redirect()
            ->route('mahasiswa.index')
            ->with('sukses', 'Pertanyaan lanjutan berhasil dikirim!');
    }

    // Menampilkan semua antrean pertanyaan mahasiswa
    public function index(Request $request)
    {
        // Hanya Data Entry yang boleh membuka halaman
        if (session('user.role') !== 'data_entry') {
            abort(403);
        }

        $query = PertanyaanLanjutan::with([
            'pertanyaan',
            'dataEntry',
            'faqBaru'
        ])
        ->latest();

        // Filter berdasarkan status jika dipilih
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian kata kunci jika ada
        if ($request->filled('q')) {
            $searchTerm = trim($request->q);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('isi_pertanyaan', 'like', "%{$searchTerm}%")
                  ->orWhere('email_mahasiswa', 'like', "%{$searchTerm}%")
                  ->orWhereHas('pertanyaan', function ($faqQuery) use ($searchTerm) {
                      $faqQuery->where('judul_pertanyaan', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Daftar seluruh pertanyaan lanjutan
        $pertanyaans = $query->paginate(10)->withQueryString();

        // Statistik seluruh pertanyaan mahasiswa
        $totalPertanyaan = PertanyaanLanjutan::count();

        $totalBaru = PertanyaanLanjutan::where('status', 'baru')->count();

        $totalDiproses = PertanyaanLanjutan::where(
            'status',
            'sedang_ditangani'
        )->count();

        $totalDijawab = PertanyaanLanjutan::where(
            'status',
            'dijawab'
        )->count();

        return view(
            'pertanyaan-lanjutan.index',
            compact(
                'pertanyaans',
                'totalPertanyaan',
                'totalBaru',
                'totalDiproses',
                'totalDijawab'
            )
        );
    }

    // Data Entry mengambil pertanyaan
    public function ambil($id)
    {
        $userId = session('user.id');

        // Pastikan akun yang mengambil adalah Data Entry
        if (session('user.role') !== 'data_entry') {
            abort(403);
        }

        $berhasil = DB::transaction(function () use ($id, $userId) {

            $pertanyaan = PertanyaanLanjutan::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            // Hanya pertanyaan baru yang bisa diambil
            if ($pertanyaan->status !== 'baru') {
                return false;
            }

            $pertanyaan->update([
                'data_entry_id' => $userId,
                'status' => 'sedang_ditangani',
                'diambil_pada' => now(),
            ]);

            return true;
        });

        if (!$berhasil) {
            return back()->with(
                'error',
                'Pertanyaan sudah diambil oleh Data Entry lain.'
            );
        }

            return redirect()
            ->route('pertanyaanLanjutan.jawab', $id)
            ->with('sukses', 'Pertanyaan berhasil diambil. Silakan tulis jawaban.');
    }

    public function jawab($id)
    {
        // Hanya Data Entry yang boleh mengakses halaman ini
        if (session('user.role') !== 'data_entry') {
            abort(403);
        }

        // Hanya Data Entry pemilik tugas yang bisa membuka
        $tugas = PertanyaanLanjutan::with([
            'pertanyaan.kategori',
            'faqBaru.tags'
        ])
        ->where('data_entry_id', session('user.id'))
        ->findOrFail($id);

        // Tugas harus masih bisa dikerjakan
        if ($tugas->status !== 'sedang_ditangani') {
            return redirect()
                ->route('pertanyaanLanjutan.index')
                ->with('error', 'Tugas ini sudah selesai atau ditutup.');
        }

        // Jika jawaban sudah dikirim dan belum dikembalikan Validator,
        // jangan izinkan Data Entry mengeditnya lagi.
        if (
            $tugas->faqBaru &&
            in_array($tugas->faqBaru->status, [
                'menunggu_validasi',
                'disetujui'
            ])
        ) {
            return redirect()
                ->route('pertanyaanLanjutan.index')
                ->with('error', 'Jawaban sudah dikirim untuk validasi.');
        }

        $tags = Tag::orderBy('nama_tag')->get();

        return view(
            'pertanyaan-lanjutan.jawab',
            compact('tugas', 'tags')
        );
    }

    
    public function simpanJawaban(Request $request, $id)
    {
        if (session('user.role') !== 'data_entry') {
            abort(403);
        }

        $validated = $request->validate([
            'judul_pertanyaan' => 'required|string|max:1000',
            'isi_jawaban' => 'required|string',
            'aksi' => 'required|in:draft,kirim',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:100',
        ]);

        $statusBaru = $validated['aksi'] === 'kirim'
            ? 'menunggu_validasi'
            : 'draft';

        $userId = session('user.id');

        DB::transaction(function () use (
            $id,
            $userId,
            $validated,
            $statusBaru
        ) {
            // Kunci tugas supaya tidak diproses bersamaan
            $tugas = PertanyaanLanjutan::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            // Hanya pemilik tugas yang boleh menyimpan
            if (
                $tugas->data_entry_id != $userId ||
                $tugas->status !== 'sedang_ditangani'
            ) {
                abort(403, 'Kamu tidak boleh mengubah tugas ini.');
            }

            $faqAsal = Pertanyaan::findOrFail(
                $tugas->pertanyaan_id
            );

            $faqBaru = $tugas->faq_baru_id
                ? Pertanyaan::findOrFail($tugas->faq_baru_id)
                : null;

            if ($faqBaru) {

                // FAQ yang sudah dikirim atau disetujui
                // tidak boleh diedit lewat halaman ini
                if (!in_array($faqBaru->status, ['draft', 'ditolak'])) {
                    abort(403, 'FAQ sedang divalidasi atau sudah diterbitkan.');
                }

                if ($faqBaru->user_id != $userId) {
                    abort(403);
                }

                // Perbarui jawaban yang sudah pernah disimpan
                $faqBaru->update([
                    'kategori_id' => $faqAsal->kategori_id,
                    'judul_pertanyaan' => $validated['judul_pertanyaan'],
                    'isi_jawaban' => $validated['isi_jawaban'],
                    'status' => $statusBaru,
                    'catatan_validator' => null,
                ]);

            } else {

                // Buat FAQ baru dari pertanyaan mahasiswa
                $faqBaru = Pertanyaan::create([
                    'kategori_id' => $faqAsal->kategori_id,
                    'user_id' => $userId,
                    'judul_pertanyaan' => $validated['judul_pertanyaan'],
                    'isi_jawaban' => $validated['isi_jawaban'],
                    'status' => $statusBaru,
                    'catatan_validator' => null,
                ]);

                // Hubungkan tiket mahasiswa ke FAQ baru
                $tugas->faq_baru_id = $faqBaru->id;
                $tugas->save();
            }

            // Simpan kata kunci / tags
            $idTags = [];

            foreach ($validated['tags'] ?? [] as $namaTag) {
                $namaTag = strtolower(trim($namaTag));

                if ($namaTag === '') {
                    continue;
                }

                $tag = Tag::firstOrCreate([
                    'nama_tag' => $namaTag
                ]);

                $idTags[] = $tag->id;
            }

            $faqBaru->tags()->sync(array_unique($idTags));
        });

        if ($statusBaru === 'draft') {
            return redirect()
                ->route('pertanyaanLanjutan.jawab', $id)
                ->with('sukses', 'Draft jawaban berhasil disimpan!');
        }

        return redirect()
            ->route('pertanyaanLanjutan.index')
            ->with(
                'sukses',
                'Jawaban berhasil dikirim dan masuk ke antrean Validator!'
            );
    }

    /**
     * Menutup pertanyaan lanjutan karena jawaban FAQ asal sudah cukup jelas.
     */
    public function tutup($id)
    {
        if (session('user.role') !== 'data_entry') {
            abort(403);
        }

        $tugas = PertanyaanLanjutan::where('data_entry_id', session('user.id'))
            ->where('status', 'sedang_ditangani')
            ->findOrFail($id);

        $tugas->update(['status' => 'ditutup']);

        return redirect()
            ->route('pertanyaanLanjutan.index')
            ->with('sukses', 'Pertanyaan berhasil ditutup. Jawaban FAQ asal dianggap sudah mencukupi.');
    }

}

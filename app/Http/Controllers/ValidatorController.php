<?php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidatorController extends Controller
{
    /**
     * Menampilkan daftar pertanyaan yang menunggu validasi.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', '1');

        $query = Pertanyaan::with([
            'kategori',
            'user',
            'validator'
        ])->latest();

        if ($status === 'semua') {
            $query->whereIn('status', [1, 2, 3]);
        } elseif (in_array((string) $status, ['1', '2', '3'], true)) {
            $query->where('status', (int) $status);
        } else {
            $status = '1';
            $query->where('status', 1);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function($q) use ($keyword) {
                $q->where('judul_pertanyaan', 'like', "%{$keyword}%")
                  ->orWhere('isi_jawaban', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function($uq) use ($keyword) {
                      $uq->where('name', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('kategori', function($kq) use ($keyword) {
                      $kq->where('nama_kategori', 'like', "%{$keyword}%");
                  });
            });
        }

        $pertanyaans = $query->paginate(10)->withQueryString();

        return view('validator.index', compact('pertanyaans', 'status'));
    }

    /**
     * Menampilkan detail pertanyaan untuk divalidasi.
     */
    public function show($id)
    {
        $pertanyaan = Pertanyaan::with(['kategori', 'user', 'tags'])->findOrFail($id);
        return view('validator.show', compact('pertanyaan'));
    }

    /**
     * Memperbarui status validasi (Disetujui / Ditolak) beserta catatan.
     */
    public function updateStatus(Request $request, $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);

        // Pastikan hanya Validator yang mengambil pertanyaan
        // yang boleh memberikan keputusan
        if ($pertanyaan->validator_id != session('user.id')) {
            return back()->with(
                'error',
                'Kamu tidak memiliki akses untuk memvalidasi pertanyaan ini.'
            );
        }

        $statusInput = $request->input('status');
        $normalizedStatus = Pertanyaan::normalizeStatus($statusInput);
        if ($normalizedStatus !== null) {
            $request->merge(['status' => $normalizedStatus]);
        }

        $validated = $request->validate([
            'status' => 'required|integer|in:2,3',
            'catatan_validator' => 'nullable|string|max:1000',
        ]);

        $pertanyaan->update([
            'status' => (int) $validated['status'],
            'catatan_validator' => $validated['catatan_validator'] ?? null,
        ]);

        // Jika FAQ berasal dari pertanyaan lanjutan mahasiswa
        if ((int) $validated['status'] === Pertanyaan::DISETUJUI) {
            \App\Models\PertanyaanLanjutan::where(
                'faq_baru_id',
                $pertanyaan->id
            )->where('status', 1)
            ->update([
                'status' => 2
            ]);
        }

        $statusText = (int) $validated['status'] === Pertanyaan::DISETUJUI ? 'disetujui' : 'ditolak';

        $redirectStatus = $request->input('redirect_status', '1');
        return redirect()->route('validator.index', ['status' => $redirectStatus])->with('sukses', "Pertanyaan berhasil {$statusText}!");
    }

    public function ambilReview(Request $request, $id)
    {
        if (session('user.role') !== 'validator') {
            abort(403);
        }

        $userId = session('user.id');

        $hasil = DB::transaction(function () use ($id, $userId) {

            $pertanyaan = Pertanyaan::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pertanyaan->status !== Pertanyaan::MENUNGGU_VALIDASI) {
                return 'selesai';
            }

            // Sudah diambil Validator lain
            if (
                $pertanyaan->validator_id !== null &&
                (int) $pertanyaan->validator_id !== (int) $userId
            ) {
                return 'diambil_orang_lain';
            }

            // Belum diambil siapa pun
            if ($pertanyaan->validator_id === null) {
                $pertanyaan->update([
                    'validator_id' => $userId,
                    'review_diambil_pada' => now(),
                ]);
            }

            return 'berhasil';
        });

        if ($hasil !== 'berhasil') {
            return back()->with(
                'error',
                $hasil === 'selesai'
                    ? 'FAQ ini sudah selesai divalidasi.'
                    : 'FAQ sedang direview oleh Validator lain.'
            );
        }

        return redirect()
            ->route('validator.index', [
                'status' => '1',
                'page' => max(1, (int) $request->input('page', 1)),
                'selected' => $id,
            ])
            ->with('sukses', 'FAQ berhasil diambil. Silakan lakukan review.');
    }
}
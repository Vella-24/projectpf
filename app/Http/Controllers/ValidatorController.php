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
        $status = $request->get('status', 'menunggu_validasi');

        $query = Pertanyaan::with([
            'kategori',
            'user',
            'validator'
        ])->latest();

        if ($status === 'semua') {
            $query->whereIn('status', ['menunggu_validasi', 'disetujui', 'ditolak']);
        } elseif (in_array($status, ['menunggu_validasi', 'disetujui', 'ditolak'])) {
            $query->where('status', $status);
        } else {
            $status = 'menunggu_validasi';
            $query->where('status', $status);
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

        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_validator' => 'nullable|string|max:1000',
        ]);

        $pertanyaan->update([
            'status' => $validated['status'],
            'catatan_validator' => $validated['catatan_validator'],
        ]);

        // Jika FAQ berasal dari pertanyaan lanjutan mahasiswa
        if ($validated['status'] === 'disetujui') {
            \App\Models\PertanyaanLanjutan::where(
                'faq_baru_id',
                $pertanyaan->id
            )->where('status', 'sedang_ditangani')
            ->update([
                'status' => 'dijawab'
            ]);
        }

        $statusText = $validated['status'] === 'disetujui' ? 'disetujui' : 'ditolak';

        $redirectStatus = $request->input('redirect_status', 'menunggu_validasi');
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

            if ($pertanyaan->status !== 'menunggu_validasi') {
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
                'status' => 'menunggu_validasi',
                'page' => max(1, (int) $request->input('page', 1)),
                'selected' => $id,
            ])
            ->with('sukses', 'FAQ berhasil diambil. Silakan lakukan review.');
    }
}
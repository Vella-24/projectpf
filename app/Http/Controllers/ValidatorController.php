<?php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use Illuminate\Http\Request;

class ValidatorController extends Controller
{
    /**
     * Menampilkan daftar pertanyaan yang menunggu validasi.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'menunggu_validasi');

        $query = Pertanyaan::with(['kategori', 'user'])->latest();

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

        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_validator' => 'nullable|string|max:1000',
        ]);

        $pertanyaan->update([
            'status' => $validated['status'],
            'catatan_validator' => $validated['catatan_validator'],
        ]);

        $statusText = $validated['status'] === 'disetujui' ? 'disetujui' : 'ditolak';

        $redirectStatus = $request->input('redirect_status', 'menunggu_validasi');
        return redirect()->route('validator.index', ['status' => $redirectStatus])->with('sukses', "Pertanyaan berhasil {$statusText}!");
    }
}
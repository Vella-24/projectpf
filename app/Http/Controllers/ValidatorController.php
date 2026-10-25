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
        $query = Pertanyaan::with(['kategori', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['menunggu_validasi', 'disetujui', 'ditolak']);
        }

        $pertanyaans = $query->paginate(10)->withQueryString();

        return view('validator.index', compact('pertanyaans'));
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

        return redirect()->route('validator.index')->with('sukses', "Pertanyaan berhasil {$statusText}!");
    }
}
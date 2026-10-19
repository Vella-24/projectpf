<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pertanyaan (Data Entry)</title>
</head>
<body>
    <h1>Edit Pertanyaan</h1>
    <p><a href="{{ route('pertanyaan.index') }}">&larr; Kembali ke Daftar Pertanyaan</a></p>

    <hr>

    @if($errors->any())
        <div style="color: red;">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pertanyaan.update', $pertanyaan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label><strong>Kategori Pertanyaan:</strong></label><br>
            <select name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id', $pertanyaan->kategori_id) == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label><strong>Judul / Teks Pertanyaan:</strong></label><br>
            <textarea name="judul_pertanyaan" rows="3" cols="70" required>{{ old('judul_pertanyaan', $pertanyaan->judul_pertanyaan) }}</textarea>
        </p>

        <p>
            <label><strong>Isi Jawaban Resmi:</strong></label><br>
            <textarea name="isi_jawaban" rows="6" cols="70" required>{{ old('isi_jawaban', $pertanyaan->isi_jawaban) }}</textarea>
        </p>

        <p>
            <label><strong>Status:</strong></label><br>
            <select name="status" required>
                <option value="draft" {{ old('status', $pertanyaan->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="menunggu_validasi" {{ old('status', $pertanyaan->status) == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                <option value="disetujui" {{ old('status', $pertanyaan->status) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ old('status', $pertanyaan->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </p>

        @if($pertanyaan->catatan_validator)
            <p style="background-color: #fff9db; padding: 10px; border: 1px solid #ffe066;">
                <strong>Catatan Validator:</strong> {{ $pertanyaan->catatan_validator }}
            </p>
        @endif

        <p>
            <button type="submit">Update Pertanyaan</button>
            <a href="{{ route('pertanyaan.index') }}">Batal</a>
        </p>
    </form>
</body>
</html>

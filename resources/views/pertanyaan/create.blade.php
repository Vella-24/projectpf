<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pertanyaan (Data Entry)</title>
</head>
<body>
    <h1>Tambah Pertanyaan Baru</h1>
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

    <form action="{{ route('pertanyaan.store') }}" method="POST">
        @csrf

        <p>
            <label><strong>Kategori Pertanyaan:</strong></label><br>
            <select name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('kategori.create') }}">+ Tambah Kategori Baru</a>
        </p>

        <p>
            <label><strong>Judul / Teks Pertanyaan:</strong></label><br>
            <textarea name="judul_pertanyaan" rows="3" cols="70" placeholder="Tuliskan pertanyaan mahasiswa..." required>{{ old('judul_pertanyaan') }}</textarea>
        </p>

        <p>
            <label><strong>Isi Jawaban Resmi:</strong></label><br>
            <textarea name="isi_jawaban" rows="6" cols="70" placeholder="Tuliskan jawaban resmi dari tim akademik..." required>{{ old('isi_jawaban') }}</textarea>
        </p>

        <p>
            <label><strong>Status:</strong></label><br>
            <select name="status" required>
                <option value="menunggu_validasi" {{ old('status', 'menunggu_validasi') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="disetujui" {{ old('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            </select>
        </p>

        <p>
            <button type="submit">Simpan Pertanyaan</button>
            <a href="{{ route('pertanyaan.index') }}">Batal</a>
        </p>
    </form>
</body>
</html>

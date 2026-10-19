<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pertanyaan (Data Entry)</title>
</head>
<body>
    <h1>Daftar Pertanyaan (FAQ) - Data Entry</h1>

    <p>
        <a href="{{ route('pertanyaan.create') }}">Tambah Pertanyaan Baru</a> |
        <a href="{{ route('kategori.index') }}">Kelola Kategori</a>
    </p>

    <hr>

    @if(session('sukses'))
        <p style="color: green;"><strong>{{ session('sukses') }}</strong></p>
    @endif

    <h3>Filter & Pencarian</h3>
    <form action="{{ route('pertanyaan.index') }}" method="GET">
        <label>Cari:</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pertanyaan / jawaban...">

        <label>Kategori:</label>
        <select name="kategori_id">
            <option value="">-- Semua Kategori --</option>
            @foreach($kategoris as $kat)
                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                    {{ $kat->nama_kategori }}
                </option>
            @endforeach
        </select>

        <label>Status:</label>
        <select name="status">
            <option value="">-- Semua Status --</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>

        <button type="submit">Filter</button>
        <a href="{{ route('pertanyaan.index') }}">Reset</a>
    </form>

    <br>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr bgcolor="#f0f0f0">
                <th>No</th>
                <th>Judul Pertanyaan</th>
                <th>Jawaban</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pertanyaans as $index => $item)
                <tr>
                    <td align="center">{{ $pertanyaans->firstItem() + $index }}</td>
                    <td><strong>{{ $item->judul_pertanyaan }}</strong></td>
                    <td>{{ $item->isi_jawaban }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $item->status }}</td>
                    <td align="center">
                        <a href="{{ route('pertanyaan.show', $item->id) }}">Detail</a> |
                        <a href="{{ route('pertanyaan.edit', $item->id) }}">Edit</a> |
                        <form action="{{ route('pertanyaan.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pertanyaan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" align="center">Tidak ada data pertanyaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    {{ $pertanyaans->links() }}
</body>
</html>

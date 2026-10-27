<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori</title>
</head>
<body>
    <h1>Daftar Kategori Pertanyaan</h1>

    <p>
        <a href="{{ route('pertanyaan.index') }}">&larr; Kembali ke Daftar Pertanyaan</a> |
        <a href="{{ route('kategori.create') }}">Tambah Kategori Baru</a>
    </p>

    <hr>

    @if(session('sukses'))
        <p style="color: green;"><strong>{{ session('sukses') }}</strong></p>
    @endif

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr bgcolor="#f0f0f0">
                <th>No</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah Pertanyaan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kategoris as $index => $kat)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $kat->nama_kategori }}</strong></td>
                    <td>{{ $kat->deskripsi ?? '-' }}</td>
                    <td align="center">
                        <a href="{{ route('pertanyaan.index', ['kategori_id' => $kat->id]) }}">
                            {{ $kat->pertanyaans_count }} Pertanyaan
                        </a>
                    </td>
                    <td align="center">
                        <a href="{{ route('kategori.edit', $kat->id) }}">Edit</a> |
                        <form action="{{ route('kategori.destroy', $kat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Menghapus kategori ini juga akan menghapus pertanyaan di dalamnya. Yakin?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" align="center">Belum ada data kategori.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

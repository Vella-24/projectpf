<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
</head>
<body>
    <h1>Edit Kategori</h1>
    <p><a href="{{ route('kategori.index') }}">&larr; Kembali ke Daftar Kategori</a></p>

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

    <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label><strong>Nama Kategori:</strong></label><br>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" size="40" required>
        </p>

        <p>
            <label><strong>Deskripsi Kategori:</strong></label><br>
            <textarea name="deskripsi" rows="3" cols="50">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
        </p>

        <p>
            <button type="submit">Update Kategori</button>
            <a href="{{ route('kategori.index') }}">Batal</a>
        </p>
    </form>
</body>
</html>

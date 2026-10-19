<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pertanyaan (Data Entry)</title>
</head>
<body>
    <h1>Detail Pertanyaan FAQ</h1>
    <p><a href="{{ route('pertanyaan.index') }}">&larr; Kembali ke Daftar Pertanyaan</a></p>

    <hr>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr>
            <th width="200" align="left" bgcolor="#f0f0f0">ID</th>
            <td>{{ $pertanyaan->id }}</td>
        </tr>
        <tr>
            <th align="left" bgcolor="#f0f0f0">Kategori</th>
            <td>{{ $pertanyaan->kategori->nama_kategori ?? '-' }}</td>
        </tr>
        <tr>
            <th align="left" bgcolor="#f0f0f0">Pertanyaan</th>
            <td><strong>{{ $pertanyaan->judul_pertanyaan }}</strong></td>
        </tr>
        <tr>
            <th align="left" bgcolor="#f0f0f0">Jawaban Resmi</th>
            <td style="white-space: pre-line;">{{ $pertanyaan->isi_jawaban }}</td>
        </tr>
        <tr>
            <th align="left" bgcolor="#f0f0f0">Status</th>
            <td>{{ $pertanyaan->status }}</td>
        </tr>
        @if($pertanyaan->catatan_validator)
            <tr>
                <th align="left" bgcolor="#f0f0f0">Catatan Validator</th>
                <td>{{ $pertanyaan->catatan_validator }}</td>
            </tr>
        @endif
        <tr>
            <th align="left" bgcolor="#f0f0f0">Petugas Input</th>
            <td>{{ $pertanyaan->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <th align="left" bgcolor="#f0f0f0">Waktu Dibuat</th>
            <td>{{ $pertanyaan->created_at }}</td>
        </tr>
        <tr>
            <th align="left" bgcolor="#f0f0f0">Waktu Diubah</th>
            <td>{{ $pertanyaan->updated_at }}</td>
        </tr>
    </table>

    <br>

    <p>
        <a href="{{ route('pertanyaan.edit', $pertanyaan->id) }}">Edit Pertanyaan</a> |
        <form action="{{ route('pertanyaan.destroy', $pertanyaan->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pertanyaan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit">Hapus Pertanyaan</button>
        </form>
    </p>
</body>
</html>

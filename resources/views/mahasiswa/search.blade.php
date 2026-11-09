<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Hasil Pencarian FAQ</h2>

<form action="{{ route('mahasiswa.search') }}" method="GET">
    <input
        type="text"
        name="q"
        value="{{ $keyword }}"
        placeholder="Cari pertanyaan..."
    >

    <button type="submit">Cari</button>
</form>

@if ($hasil->isEmpty())
    <p>FAQ yang sesuai belum ditemukan.</p>
@else
    @foreach ($hasil as $item)
        <div class="faq-item">
            <h3>{{ $item->pertanyaan }}</h3>
            <p>{{ $item->jawaban }}</p>
        </div>
    @endforeach
@endif
</body>
</html>
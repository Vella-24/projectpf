<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pertanyaan - Validator ASK UBAYA</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f5f5; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        h2 { color: #781f2b; margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        .box { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; }
        select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; box-sizing: border-box; }
        .btn-submit { background: #781f2b; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 15px; }
        .btn-submit:hover { background: #5f1721; }
        .btn-back { background: #6c757d; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; display: inline-block; margin-bottom: 15px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('validator.index') }}" class="btn-back">&larr; Kembali ke Daftar</a>
        <h2>Review Pertanyaan & Jawaban</h2>

        <div class="form-group">
            <label>Kategori:</label>
            <p>{{ $pertanyaan->kategori->nama_kategori }}</p>
        </div>

        <div class="form-group">
            <label>Judul Pertanyaan:</label>
            <div class="box">{{ $pertanyaan->judul_pertanyaan }}</div>
        </div>

        <div class="form-group">
            <label>Isi Jawaban Resmi:</label>
            <div class="box">{!! nl2br(e($pertanyaan->isi_jawaban)) !!}</div>
        </div>

        <form action="{{ route('validator.updateStatus', $pertanyaan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="status">Keputusan Validator:</label>
                <select name="status" id="status" required>
                    <option value="disetujui" {{ $pertanyaan->status == 'disetujui' ? 'selected' : '' }}>Setujui (Publish)</option>
                    <option value="ditolak" {{ $pertanyaan->status == 'ditolak' ? 'selected' : '' }}>Tolak / Perlu Revisi</option>
                </select>
            </div>

            <div class="form-group">
                <label for="catatan_validator">Catatan Validator (Opsional):</label>
                <textarea name="catatan_validator" id="catatan_validator" rows="4" placeholder="Tuliskan catatan revisi jika ditolak...">{{ $pertanyaan->catatan_validator }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Simpan Keputusan</button>
        </form>
    </div>
</body>
</html>
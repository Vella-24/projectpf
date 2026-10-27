<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Validator - ASK UBAYA</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f5f5; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        h2 { color: #781f2b; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; font-size: 14px; }
        th { background-color: #781f2b; color: white; }
        .badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .badge-menunggu_validasi { background: #ff9800; color: white; }
        .badge-disetujui { background: #4caf50; color: white; }
        .badge-ditolak { background: #f44336; color: white; }
        .btn { padding: 6px 12px; background: #781f2b; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .btn:hover { background: #5f1721; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .logout-btn { float: right; background: #c62828; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/login" class="logout-btn">Logout</a>
        <h2>Panel Validator Akademik</h2>
        <p>Daftar pertanyaan yang masuk untuk divalidasi sebelum dipublikasikan.</p>

        @if(session('sukses'))
            <div class="alert-success">{{ session('sukses') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Judul Pertanyaan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pertanyaans as $index => $p)
                <tr>
                    <td>{{ $pertanyaans->firstItem() + $index }}</td>
                    <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ Str::limit($p->judul_pertanyaan, 50) }}</td>
                    <td>
                        <span class="badge badge-{{ $p->status }}">
                            {{ ucwords(str_replace('_', ' ', $p->status)) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('validator.show', $p->id) }}" class="btn">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada pertanyaan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">

        @if ($pertanyaans->onFirstPage())
            <span style="color: #999;">&laquo; Previous</span>
        @else
            <a href="{{ $pertanyaans->previousPageUrl() }}">
                &laquo; Previous
            </a>
        @endif

        @if ($pertanyaans->hasMorePages())
            <a href="{{ $pertanyaans->nextPageUrl() }}">
                Next &raquo;
            </a>
        @else
            <span style="color: #999;">Next &raquo;</span>
        @endif

    </div>
    </div>
</body>
</html>
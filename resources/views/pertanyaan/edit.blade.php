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
            <label for="input-tag"><strong>Kata Kunci / Tag:</strong></label><br>

            <input type="text" id="input-tag" list="saran-tag" placeholder="Ketik kata kunci..." autocomplete="off">

            <datalist id="saran-tag">
                @foreach ($tags as $tag)
                    <option value="{{ $tag->nama_tag }}"></option>
                @endforeach
            </datalist>

            <button type="button" id="tambah-tag">Tambah</button>
        </p>

        <div id="daftar-tag">
            @foreach (
                old('tags', $pertanyaan->tags->pluck('nama_tag')->toArray())
                as $namaTag
            )
                <span style="display: inline-block; margin: 4px; padding: 5px; border: 1px solid #ccc;">
                    {{ $namaTag }}
                    <button type="button" class="hapus-tag">×</button>
                    <input type="hidden" name="tags[]" value="{{ $namaTag }}">
                </span>
            @endforeach
        </div>


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

<!-- java script e -->
<script>
    const inputTag = document.getElementById('input-tag');
    const daftarTag = document.getElementById('daftar-tag');
    const tombolTambah = document.getElementById('tambah-tag');

    function tambahTag() {
        const namaTag = inputTag.value.trim().toLowerCase();

        if (namaTag === '') return;

        const tagSudahAda = Array.from(
            daftarTag.querySelectorAll('input[name="tags[]"]')
        ).some(function(input) {
            return input.value.toLowerCase() === namaTag;
        });

        if (tagSudahAda) {
            inputTag.value = '';
            return;
        }

        const item = document.createElement('span');
        item.style.display = 'inline-block';
        item.style.margin = '4px';
        item.style.padding = '5px';
        item.style.border = '1px solid #ccc';

        const teks = document.createElement('span');
        teks.textContent = namaTag + ' ';

        const tombolHapus = document.createElement('button');
        tombolHapus.type = 'button';
        tombolHapus.textContent = '×';

        const inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = 'tags[]';
        inputHidden.value = namaTag;

        tombolHapus.addEventListener('click', function() {
            item.remove();
        });

        item.appendChild(teks);
        item.appendChild(tombolHapus);
        item.appendChild(inputHidden);

        daftarTag.appendChild(item);
        inputTag.value = '';
    }

    tombolTambah.addEventListener('click', tambahTag);

    inputTag.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            tambahTag();
        }
    });

    daftarTag.addEventListener('click', function(event) {
        if (event.target.classList.contains('hapus-tag')) {
            event.target.parentElement.remove();
        }
    });
</script>

</body>
</html>

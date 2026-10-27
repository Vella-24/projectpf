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
                old(
                    'tags',
                    isset($pertanyaan)
                        ? $pertanyaan->tags->pluck('nama_tag')->toArray()
                        : []
                ) as $namaTag
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

<!-- java script e -->
<script>
    const inputTag = document.getElementById('input-tag');
    const daftarTag = document.getElementById('daftar-tag');
    const tombolTambah = document.getElementById('tambah-tag');

    function tambahTag() {
        const namaTag = inputTag.value.trim().toLowerCase();

        if (namaTag === '') {
            return;
        }

        // Cek agar tag yang sama tidak ditambahkan dua kali
        const tagSudahAda = Array.from(
            daftarTag.querySelectorAll('input[name="tags[]"]')
        ).some(function(input) {
            return input.value.toLowerCase() === namaTag;
        });

        if (tagSudahAda) {
            inputTag.value = '';
            return;
        }

        // Membuat tampilan tag
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

        // Input tersembunyi yang dikirim ke Laravel
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

    // Tekan Enter untuk menambahkan tag yang dipilih atau diketik
    inputTag.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            tambahTag();
        }
    });

    // Tombol hapus untuk tag yang sudah tampil saat halaman dibuka
    daftarTag.addEventListener('click', function(event) {
        if (event.target.classList.contains('hapus-tag')) {
            event.target.parentElement.remove();
        }
    });
</script>


</body>
</html>

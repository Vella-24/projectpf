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
            <label><strong>Kata kunci:</strong></label><br>

            <div id="daftar-tag"
                style="border: 1px solid #ccc; padding: 10px; border-radius: 8px; min-height: 45px;">

                @foreach(old('tags', []) as $tag)
                    <span class="tag-item"
                        style="display: inline-block; background: #fce7ef; padding: 6px 10px; margin: 3px; border-radius: 6px;">
                        {{ $tag }}
                        <button type="button" class="hapus-tag">×</button>
                    </span>
                    <input type="hidden" name="tags[]" value="{{ $tag }}">
                @endforeach

                <input
                    type="text"
                    id="input-tag"
                    placeholder="Ketik kata kunci lalu tekan Enter"
                    style="border: none; outline: none; padding: 8px; min-width: 250px;"
                >
            </div>
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

<!-- java script e -->
<script>
    const inputTag = document.getElementById('input-tag');
    const daftarTag = document.getElementById('daftar-tag');

    function tambahTag(nama) {
        nama = nama.trim();

        if (nama === '') {
            return;
        }

        //cek agar kata kunci yang sama tidak ditambahkan dua kali
        const tagAda = Array.from(
            daftarTag.querySelectorAll('input[name="tags[]"]')
        ).some(input =>
            input.value.toLowerCase() === nama.toLowerCase()
        );

        if (tagAda) {
            inputTag.value = '';
            return;
        }

        //membuat tampilan tag
        const label = document.createElement('span');
        label.className = 'tag-item';
        label.style.cssText =
            'display:inline-block;background:#fce7ef;padding:6px 10px;margin:3px;border-radius:6px;';

        const teks = document.createTextNode(nama + ' ');
        label.appendChild(teks);

        const tombol = document.createElement('button');
        tombol.type = 'button';
        tombol.className = 'hapus-tag';
        tombol.textContent = '×';

        label.appendChild(tombol);

        // Membuat input tersembunyi agar tag dikirim ke Laravel
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'tags[]';
        hidden.value = nama;

        daftarTag.insertBefore(label, inputTag);
        daftarTag.insertBefore(hidden, inputTag);

        inputTag.value = '';
    }

    //tambahkan tag ketika tombol Enter ditekan
    inputTag.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            tambahTag(inputTag.value);
        }
    });

    //hapus tag ketika tombol × ditekan
    daftarTag.addEventListener('click', function(event) {
        if (event.target.classList.contains('hapus-tag')) {
            const label = event.target.closest('.tag-item');
            const hidden = label.nextElementSibling;

            if (hidden && hidden.name === 'tags[]') {
                hidden.remove();
            }

            label.remove();
        }
    });
</script>


</body>
</html>

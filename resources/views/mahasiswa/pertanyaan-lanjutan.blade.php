
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ajukan Pertanyaan - ASK UBAYA</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background-color: #f8f6f6;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            color: #1f2937;
        }

        .form-card {
            background: white;
            width: 100%;
            max-width: 480px;
            padding: 32px;
            border-radius: 16px;
            border: 1px solid #e5e0e0;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        }

        .form-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .form-header h1 {
            margin: 0 0 10px;
            color: #801d32;
            font-size: 25px;
            font-weight: 700;
        }

        .form-header p {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 19px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 13px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            font-family: inherit;
            font-size: 14px;
            color: #374151;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: #801d32;
            box-shadow: 0 0 0 3px rgba(128, 29, 50, 0.12);
        }

        textarea.form-control {
            min-height: 115px;
            resize: vertical;
            line-height: 1.6;
        }

        .faq-asal {
            background: #faf7f8;
            border: 1px solid #f0e5e8;
            color: #4b5563;
            cursor: default;
        }

        .submit-button {
            display: block;
            width: 100%;
            background: #801d32;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        .submit-button:hover {
            background: #641628;
        }

        .back-link {
            display: block;
            margin-top: 18px;
            text-align: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 13px;
        }

        .back-link:hover {
            color: #801d32;
        }

        .error-text {
            color: #be123c;
            font-size: 12px;
            margin-top: 6px;
        }

        .required {
            color: #be123c;
        }
    </style>
</head>

<body>

    <div class="form-card">

        <!-- HEADER -->
        <div class="form-header" style="text-align: center;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo ASK UBAYA" style="width: 48px; height: 48px; border-radius: 14px; margin: 0 auto 10px auto; display: block; box-shadow: 0 4px 12px rgba(124, 18, 36, 0.2);">
            <h1>ASK UBAYA</h1>
            <p>
                Ajukan Pertanyaan Lebih Lanjut
                <br>
                Silakan jelaskan hal yang masih ingin kamu ketahui.
            </p>
        </div>

        <!-- FORM -->
        <form action="{{ route('mahasiswa.pertanyaanLanjutan.store') }}"
              method="POST">

            @csrf

            <input type="hidden"
                   name="pertanyaan_id"
                   value="{{ $pertanyaan->id }}">

            <!-- FAQ TERKAIT -->
            <div class="form-group">
                <label>FAQ yang sedang ditanyakan</label>

                <textarea class="form-control faq-asal"
                          rows="2"
                          readonly>{{ $pertanyaan->judul_pertanyaan }}</textarea>
            </div>

            <!-- EMAIL MAHASISWA -->
            <div class="form-group">
                <label for="email_mahasiswa">
                    Email Mahasiswa <span class="required">*</span>
                </label>

                <input type="email"
                       id="email_mahasiswa"
                       name="email_mahasiswa"
                       class="form-control"
                       placeholder="Masukkan email kamu"
                       value="{{ old('email_mahasiswa') }}"
                       required>

                @error('email_mahasiswa')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- PERTANYAAN LANJUTAN -->
            <div class="form-group">
                <label for="isi_pertanyaan">
                    Pertanyaan Lebih Lanjut <span class="required">*</span>
                </label>

                <textarea id="isi_pertanyaan"
                          name="isi_pertanyaan"
                          class="form-control"
                          placeholder="Tuliskan pertanyaan kamu secara jelas..."
                          required>{{ old('isi_pertanyaan') }}</textarea>

                @error('isi_pertanyaan')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- SUBMIT -->
            <button type="submit" class="submit-button">
                Kirim Pertanyaan
            </button>

            <!-- KEMBALI -->
            <a href="{{ route('mahasiswa.index') }}"
               class="back-link">
                ← Kembali ke Daftar FAQ
            </a>

        </form>

    </div>

</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Super Admin</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f4f6f9;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        h1 {
            color: #801F2D;
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #801F2D;
        }

        .error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .hint {
            color: #777;
            font-size: 12px;
            margin-top: 5px;
        }

        .btn {
            display: inline-block;
            padding: 11px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-save {
            background: #801F2D;
            color: white;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Edit Pengguna</h1>
    <p>Ubah informasi akun pengguna.</p>

    <form action="{{ route('superadmin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
            >

            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Email</label>
            <input
                type="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
            >

            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Password Baru</label>
            <input
                type="password"
                name="password"
                placeholder="Kosongkan jika tidak ingin mengganti password"
            >

            <div class="hint">
                Isi hanya jika ingin mengganti password.
            </div>

            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input
                type="password"
                name="password_confirmation"
                placeholder="Ulangi password baru"
            >
        </div>

        <div class="form-group">
            <label>Role Pengguna</label>

            <select name="role" required>
                <option value="data_entry"
                    {{ old('role', $user->role) == 'data_entry' ? 'selected' : '' }}>
                    Data Entry
                </option>

                <option value="validator"
                    {{ old('role', $user->role) == 'validator' ? 'selected' : '' }}>
                    Validator
                </option>

                <option value="super_admin"
                    {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>
                    Super Admin
                </option>
            </select>

            @error('role')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-save">
                Simpan Perubahan
            </button>

            <a href="{{ route('superadmin.index') }}" class="btn btn-back">
                Kembali
            </a>
        </div>

    </form>

</div>

</body>
</html>

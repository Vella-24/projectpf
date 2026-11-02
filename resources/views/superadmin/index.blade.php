
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Super Admin</title>

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
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            color: #801F2D;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-add {
            background: #801F2D;
            color: white;
        }

        .btn-edit {
            background: #f0ad4e;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.85;
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #d4edda;
            color: #155724;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #801F2D;
            color: white;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .actions form {
            margin: 0;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #777;
        }

        .role {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 4px;
            background: #e9ecef;
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <div>
            <h1>Kelola Pengguna</h1>
            <p>Halaman pengelolaan akun Data Entry dan Validator.</p>
        </div>

        <a href="{{ route('superadmin.users.create') }}" class="btn btn-add">
            + Tambah Pengguna
        </a>
    </div>

    @if(session('sukses'))
        <div class="alert">
            {{ session('sukses') }}
        </div>
    @endif

    @if($users->count() > 0)

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">

                                <a href="{{ route('superadmin.users.edit', $user->id) }}"
                                   class="btn btn-edit">
                                    Edit
                                </a>

                                <form action="{{ route('superadmin.users.destroy', $user->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah kamu yakin ingin menghapus pengguna ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-delete">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else

        <div class="empty">
            Belum ada data pengguna.
        </div>

    @endif

</div>

</body>
</html>

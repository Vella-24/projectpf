
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staf - ASK UBAYA</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f5f5;
            font-family: Arial, sans-serif;
        }

        .login-container {
            width: 90%;
            max-width: 400px;
            padding: 30px;
            background: white;
            border: 1px solid #e5dede;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            color: #781f2b;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            color: #333;
        }

        input {
            width: 100%;
            height: 45px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #781f2b;
        }

        .login-button {
            width: 100%;
            height: 45px;
            border: none;
            border-radius: 7px;
            background: #781f2b;
            color: white;
            font-size: 15px;
            cursor: pointer;
        }

        .login-button:hover {
            background: #5f1721;
        }

        .error-message {
            margin-top: 15px;
            color: #c62828;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>ASK UBAYA</h2>
        <p class="subtitle">Login Portal Staf</p>

        <form action="/login" method="POST">
            @csrf

            <div class="input-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    required
                >
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="login-button">
                Login
            </button>
        </form>

        @if (session('error'))
            <p class="error-message">
                {{ session('error') }}
            </p>
        @endif

        @if ($errors->any())
            <p class="error-message">
                {{ $errors->first() }}
            </p>
        @endif
    </div>
</body>
</html>

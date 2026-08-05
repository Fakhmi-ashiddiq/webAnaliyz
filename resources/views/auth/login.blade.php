<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Web Analyzer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --danger: #ef4444;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 700;
        }

        .login-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            background: var(--bg-color);
            color: var(--text-main);
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: var(--primary-hover);
        }

        .error-message {
            color: var(--danger);
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .demo-credentials {
            margin-top: 30px;
            padding: 15px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px dashed var(--primary);
            border-radius: 6px;
            font-size: 13px;
            text-align: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }

        .demo-credentials:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: var(--primary-hover);
        }

        .demo-credentials strong {
            color: var(--text-main);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Web Analyzer</h1>
                <p>Silakan masuk ke akun Anda</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') ?: 'admin@webanalyzer.com' }}" required autofocus placeholder="Masukkan email">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" value="admin123" required placeholder="Masukkan password">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="demo-credentials" onclick="fillDemoCredentials()" title="Klik untuk mengisi otomatis">
                <p style="margin: 0 0 10px 0; font-weight: 600; color: #5dade2;">Gunakan Akun Berikut (klik untuk mengisi otomatis):</p>
                Email: <strong>admin@webanalyzer.com</strong><br>
                Password: <strong>&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</strong>
            </div>
        </div>
    </div>

    <script>
        function fillDemoCredentials() {
            document.getElementById('email').value = 'admin@webanalyzer.com';
            document.getElementById('password').value = 'admin123';
        }
    </script>

</body>
</html>

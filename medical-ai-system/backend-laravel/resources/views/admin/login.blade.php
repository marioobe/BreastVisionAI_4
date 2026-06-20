<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — BreastVisionAI 4</title>
    @vite('resources/css/app.css')
    <style>
        :root {
            --bg-deep: #0B1220;
            --bg-panel: #101A2C;
            --bg-panel-light: #16223A;
            --line: rgba(148, 175, 209, 0.14);
            --line-bright: rgba(148, 175, 209, 0.28);
            --text-primary: #EDF2FA;
            --text-secondary: #9FB1CC;
            --text-muted: #5E7090;
            --teal: #2DD4BF;
            --teal-dim: rgba(45, 212, 191, 0.14);
            --red: #F2737A;
            --font-display: 'Fraunces', serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body {
            height: 100%;
            background: var(--bg-deep);
            color: var(--text-primary);
            font-family: var(--font-body);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 25% 30%, rgba(45,212,191,0.05) 0, transparent 40%),
                radial-gradient(circle at 75% 70%, rgba(56,189,248,0.04) 0, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }
        .login-card {
            position: relative;
            z-index: 1;
            width: 400px;
            max-width: 92%;
            background: var(--bg-panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 44px 36px 38px;
        }
        .login-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-display);
            font-size: 21px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .login-logo svg {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
        }
        .login-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 12.5px;
            font-family: var(--font-mono);
            color: var(--text-secondary);
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            background: var(--bg-deep);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: var(--font-body);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: var(--teal);
        }
        .form-group input::placeholder {
            color: var(--text-muted);
        }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--teal);
            color: #062220;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-body);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-top: 6px;
        }
        .btn-login:hover { background: #5EEAD4; }
        .btn-login:active { transform: scale(0.98); }
        .error-box {
            background: rgba(242,115,122,0.1);
            border: 1px solid rgba(242,115,122,0.25);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--red);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 12.5px;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--text-secondary); }
        @media (max-width: 480px) {
            .login-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <svg viewBox="0 0 28 28" fill="none">
                <circle cx="14" cy="14" r="10" stroke="#2DD4BF" stroke-width="1.4" opacity="0.3"/>
                <circle cx="14" cy="14" r="6" stroke="#2DD4BF" stroke-width="1.4" opacity="0.6"/>
                <circle cx="14" cy="14" r="2.5" fill="#2DD4BF"/>
                <path d="M14 4v3M14 21v3M4 14h3M21 14h3" stroke="#2DD4BF" stroke-width="1.2" stroke-linecap="round" opacity="0.5"/>
            </svg>
            BreastVision<span style="color:var(--text-muted); font-weight:400;">AI 4</span>
        </div>
        <div class="login-sub">Panel administrasi · masukkan kredensial</div>

        @if($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf
            <div class="form-group">
                <label for="email">EMAIL</label>
                <input type="email" id="email" name="email" placeholder="admin@medical-ai.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">PASSWORD</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <a href="{{ route('landing') }}" class="back-link">← Kembali ke Beranda</a>
    </div>
</body>
</html>

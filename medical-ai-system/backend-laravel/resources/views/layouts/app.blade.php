<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BreastVisionAI 4') — Klasifikasi USG Payudara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @stack('styles')
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
            --blue: #38BDF8;
            --amber: #F5B454;
            --rose: #F2737A;
            --font-display: 'Fraunces', serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--bg-deep);
            color: var(--text-primary);
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 18% 22%, rgba(45,212,191,0.05) 0, transparent 38%),
                radial-gradient(circle at 82% 68%, rgba(56,189,248,0.045) 0, transparent 42%);
            pointer-events: none;
            z-index: 0;
        }
        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 32px;
            position: relative;
            z-index: 1;
        }
        a { color: inherit; text-decoration: none; }

        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(11, 18, 32, 0.78);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }
        nav {
            max-width: 1180px;
            margin: 0 auto;
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 40px;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }
        .logo-mark {
            width: 28px; height: 28px;
            flex-shrink: 0;
        }
        .nav-links {
            display: flex;
            gap: 30px;
            font-size: 14.5px;
            color: var(--text-secondary);
        }
        .nav-links a {
            transition: color 0.2s;
            position: relative;
            padding-bottom: 6px;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 1.5px;
            background: var(--teal);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.25s ease;
        }
        .nav-links a:hover { color: var(--text-primary); }
        .nav-links a:hover::after { transform: scaleX(1); }
        .nav-links a.active { color: var(--text-primary); }
        .nav-links a.active::after { transform: scaleX(1); }
        .nav-cta {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .btn-ghost {
            font-size: 14px;
            color: var(--text-secondary);
            padding: 9px 16px;
            border: 1px solid var(--line-bright);
            border-radius: 7px;
            transition: all 0.2s;
        }
        .btn-ghost:hover { color: var(--text-primary); border-color: var(--text-secondary); }
        .btn-primary {
            font-size: 14px;
            font-weight: 600;
            color: #062220;
            background: var(--teal);
            padding: 10px 18px;
            border-radius: 7px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover { background: #5EEAD4; transform: translateY(-1px); }
        .btn-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 860px) {
            .nav-links { display: none; }
            .nav-right { gap: 0; }
        }

        section { padding: 100px 0; position: relative; }
        .section-head { max-width: 640px; margin-bottom: 64px; }
        .section-eyebrow {
            font-family: var(--font-mono);
            font-size: 12px;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 16px;
            display: block;
        }
        .section-eyebrow::before { content: '// '; color: var(--teal); }
        h2 {
            font-family: var(--font-display);
            font-weight: 500;
            font-size: 36px;
            line-height: 1.2;
            letter-spacing: -0.01em;
            margin-bottom: 16px;
        }
        .section-sub {
            font-size: 15.5px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        footer {
            border-top: 1px solid var(--line);
            padding: 48px 0 36px;
        }
        .footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 36px;
        }
        .footer-disclaimer {
            max-width: 460px;
            font-size: 12.5px;
            color: var(--text-muted);
            line-height: 1.6;
        }
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid var(--line);
            font-size: 12.5px;
            color: var(--text-muted);
            font-family: var(--font-mono);
        }

        @media (max-width: 900px) {
            section { padding: 64px 0; }
            .footer-grid { flex-direction: column; gap: 24px; }
            .footer-bottom { flex-direction: column; gap: 16px; }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('landing') }}" class="logo">
                <svg class="logo-mark" viewBox="0 0 28 28" fill="none">
                    <circle cx="14" cy="14" r="10" stroke="#2DD4BF" stroke-width="1.4" opacity="0.3"/>
                    <circle cx="14" cy="14" r="6" stroke="#2DD4BF" stroke-width="1.4" opacity="0.6"/>
                    <circle cx="14" cy="14" r="2.5" fill="#2DD4BF"/>
                    <path d="M14 4v3M14 21v3M4 14h3M21 14h3" stroke="#2DD4BF" stroke-width="1.2" stroke-linecap="round" opacity="0.5"/>
                </svg>
                BreastVision<span style="color:var(--text-muted); font-weight:400;">AI 4</span>
            </a>
            <div class="nav-right">
                <div class="nav-links" id="navLinks">
                    <a href="{{ route('landing') }}" class="{{ request()->routeIs('landing') ? 'active' : '' }}">Beranda</a>
                    @guest('admin')
                        <a href="{{ route('pasien.form') }}" class="{{ request()->routeIs('pasien.form', 'pasien.predict', 'pasien.hasil*') ? 'active' : '' }}">Pemeriksaan</a>
                    @endguest
                    @auth('admin')
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard', 'admin.*') ? 'active' : '' }}">Panel Admin</a>
                    @endauth
                </div>
                <div class="nav-cta">
                    @auth('admin')
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-ghost" style="cursor:pointer; font-family:inherit; font-size:14px; padding:9px 16px;">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    @if(!request()->routeIs('dashboard', 'admin.*', 'login'))
    <footer>
        <div class="wrap">
            <div class="footer-grid">
                <div class="logo">
                    <svg class="logo-mark" viewBox="0 0 28 28" fill="none">
                        <circle cx="14" cy="14" r="10" stroke="#2DD4BF" stroke-width="1.4" opacity="0.3"/>
                        <circle cx="14" cy="14" r="6" stroke="#2DD4BF" stroke-width="1.4" opacity="0.6"/>
                        <circle cx="14" cy="14" r="2.5" fill="#2DD4BF"/>
                        <path d="M14 4v3M14 21v3M4 14h3M21 14h3" stroke="#2DD4BF" stroke-width="1.2" stroke-linecap="round" opacity="0.5"/>
                    </svg>
                    BreastVision<span style="color:var(--text-muted); font-weight:400;">AI 4</span>
                </div>
                <p class="footer-disclaimer">
                    BreastVisionAI 4 adalah alat bantu klasifikasi awal berbasis kecerdasan buatan dan tidak menggantikan pemeriksaan medis profesional. Selalu konsultasikan hasil dengan dokter atau radiolog.
                </p>
            </div>
            <div class="footer-bottom">
                <span>© 2026 BREASTVISIONAI 4 — PROYEK AKADEMIK</span>
                @guest('admin')
                    <a href="{{ route('login') }}" class="btn-ghost" style="font-family: var(--font-mono); font-size: 12px; padding: 7px 14px;">Masuk Admin →</a>
                @endguest
            </div>
        </div>
    </footer>
    @endif

    @stack('scripts')
</body>
</html>

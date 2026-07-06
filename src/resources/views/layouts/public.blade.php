<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LaundryKita - Sistem Manajemen Laundry')</title>
    <style>
        :root {
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --soft: #f8fafc;
            --panel: #ffffff;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --mint: #14b8a6;
            --warning: #f59e0b;
            --danger: #ef4444;
            --success: #16a34a;
            --shadow: 0 18px 45px rgba(15, 23, 42, .08);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: var(--soft);
            line-height: 1.6;
        }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }
        .container { width: min(1120px, calc(100% - 32px)); margin: 0 auto; }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .94);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(16px);
        }
        .nav {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            letter-spacing: 0;
        }
        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--mint));
            font-weight: 900;
        }
        .brand small { display: block; color: var(--muted); font-size: 12px; font-weight: 600; }
        .nav-links { display: flex; align-items: center; gap: 22px; color: #374151; font-weight: 650; font-size: 14px; }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 16px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 750;
            cursor: pointer;
            transition: .2s ease;
            font-size: 14px;
        }
        .btn-primary { background: var(--primary); color: white; box-shadow: 0 12px 24px rgba(37, 99, 235, .18); }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: white; border-color: var(--line); color: var(--ink); }
        .btn-outline:hover { border-color: #cbd5e1; transform: translateY(-1px); }
        .btn-dark { background: #111827; color: white; }
        .btn-block { width: 100%; }
        .hero {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid var(--line);
        }
        .hero-grid {
            min-height: 560px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(360px, .95fr);
            align-items: center;
            gap: 46px;
            padding: 52px 0 40px;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border: 1px solid #dbeafe;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 750;
            margin-bottom: 18px;
        }
        h1, h2, h3 { line-height: 1.14; letter-spacing: 0; margin: 0; }
        h1 { font-size: clamp(38px, 6vw, 68px); max-width: 720px; }
        h2 { font-size: clamp(28px, 4vw, 42px); }
        h3 { font-size: 20px; }
        .lead { color: #4b5563; font-size: 18px; max-width: 620px; margin: 18px 0 28px; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .hero-art {
            position: relative;
            min-height: 420px;
            display: grid;
            place-items: center;
        }
        .hero-art img {
            border-radius: 8px;
            box-shadow: var(--shadow);
            border: 1px solid #edf2f7;
            background: white;
        }
        .trust-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin: 28px 0 0;
        }
        .trust-item, .stat-card, .service-card, .info-card, .panel, .auth-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .04);
        }
        .trust-item { padding: 14px; display: flex; gap: 12px; align-items: flex-start; }
        .icon {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #eff6ff;
            color: var(--primary);
            font-weight: 900;
            flex: 0 0 auto;
        }
        .muted { color: var(--muted); }
        .section { padding: 64px 0; }
        .section-head { display: flex; justify-content: space-between; gap: 24px; align-items: end; margin-bottom: 28px; }
        .section-head p { color: var(--muted); margin: 10px 0 0; max-width: 620px; }
        .service-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
        .service-card { padding: 22px; display: flex; flex-direction: column; gap: 14px; }
        .service-card .price { font-weight: 900; font-size: 22px; color: var(--primary); }
        .service-meta { display: flex; flex-wrap: wrap; gap: 8px; }
        .chip {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #334155;
            font-size: 12px;
            font-weight: 750;
        }
        .steps { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .step { padding: 20px; border-top: 3px solid var(--primary); }
        .status-box { padding: 22px; display: grid; grid-template-columns: 1fr auto; gap: 12px; align-items: center; }
        .form-control {
            width: 100%;
            min-height: 46px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 13px;
            font: inherit;
            background: white;
        }
        textarea.form-control { min-height: 110px; resize: vertical; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-weight: 750; margin-bottom: 7px; }
        .error { color: var(--danger); font-size: 13px; margin-top: 6px; }
        .alert { border-radius: 8px; padding: 13px 15px; margin-bottom: 18px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; }
        .footer { border-top: 1px solid var(--line); background: white; padding: 34px 0; color: #475569; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 28px; }
        .auth-wrap { min-height: calc(100vh - 72px); display: grid; place-items: center; padding: 48px 0; }
        .auth-card { width: min(980px, 100%); display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; }
        .auth-visual { background: #f1f5f9; padding: 38px; display: flex; flex-direction: column; justify-content: center; gap: 20px; }
        .auth-visual img { border-radius: 8px; border: 1px solid #e2e8f0; }
        .auth-form { padding: 38px; background: white; }
        .app-shell { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 24px; padding: 28px 0 56px; }
        .sidebar { background: white; border: 1px solid var(--line); border-radius: 8px; padding: 14px; align-self: start; position: sticky; top: 92px; }
        .sidebar a, .sidebar button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 0;
            background: transparent;
            padding: 11px 12px;
            border-radius: 8px;
            color: #475569;
            font-weight: 750;
            text-align: left;
            cursor: pointer;
        }
        .sidebar a.active, .sidebar a:hover, .sidebar button:hover { background: #eff6ff; color: var(--primary); }
        .content-stack { display: grid; gap: 18px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .stat-card { padding: 18px; }
        .stat-card strong { display: block; font-size: 28px; line-height: 1; margin-top: 8px; }
        .panel { padding: 22px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid var(--line); vertical-align: middle; }
        th { font-size: 12px; text-transform: uppercase; color: #64748b; letter-spacing: .04em; }
        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }
        .badge.gray { background: #f1f5f9; color: #475569; }
        .badge.info { background: #dbeafe; color: #1d4ed8; }
        .badge.warning { background: #fef3c7; color: #92400e; }
        .badge.success { background: #dcfce7; color: #166534; }
        .badge.danger { background: #fee2e2; color: #991b1b; }
        .timeline { display: grid; gap: 14px; margin-top: 18px; }
        .timeline-item { display: grid; grid-template-columns: 34px minmax(0, 1fr); gap: 12px; }
        .timeline-dot { width: 18px; height: 18px; border-radius: 50%; background: #cbd5e1; margin: 4px auto 0; }
        .timeline-item.done .timeline-dot { background: var(--primary); box-shadow: 0 0 0 5px #dbeafe; }
        .order-form-grid { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 20px; align-items: start; }
        .choice-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .choice-card { border: 1px solid var(--line); border-radius: 8px; padding: 15px; background: white; cursor: pointer; }
        .choice-card:has(input:checked) { border-color: var(--primary); box-shadow: 0 0 0 3px #dbeafe; }
        .choice-card input { margin-right: 8px; }

        @media (max-width: 940px) {
            .hero-grid, .auth-card, .app-shell, .order-form-grid { grid-template-columns: 1fr; }
            .hero-grid { min-height: auto; }
            .hero-art { min-height: auto; }
            .nav { align-items: flex-start; padding: 14px 0; flex-direction: column; }
            .nav-links { flex-wrap: wrap; gap: 12px; }
            .trust-row, .service-grid, .steps, .stats-grid, .choice-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sidebar { position: static; }
        }
        @media (max-width: 640px) {
            .container { width: min(100% - 24px, 1120px); }
            .trust-row, .service-grid, .steps, .stats-grid, .choice-grid, .footer-grid { grid-template-columns: 1fr; }
            .section-head { display: block; }
            .status-box { grid-template-columns: 1fr; }
            h1 { font-size: 38px; }
            .auth-form, .auth-visual { padding: 24px; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark">LK</span>
                <span>LaundryKita<small>Bersih, Rapi, Wangi</small></span>
            </a>
            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('services.index') }}">Layanan</a>
                <a href="{{ route('status.form') }}">Cek Status Cucian</a>
                <a href="{{ route('home') }}#cara-kerja">Tentang Kami</a>
                <a href="{{ route('home') }}#kontak">Kontak</a>
            </nav>
            <div class="nav-actions">
                @auth
                    <a class="btn btn-outline" href="{{ route('customer.dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-dark" type="submit">Logout</button>
                    </form>
                @else
                    <a class="btn btn-outline" href="{{ route('login') }}">Masuk</a>
                    <a class="btn btn-dark" href="{{ route('register') }}">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer id="kontak" class="footer">
        <div class="container footer-grid">
            <div>
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-mark">LK</span>
                    <span>LaundryKita<small>Bersih, Rapi, Wangi</small></span>
                </a>
                <p class="muted">Sistem manajemen laundry berbasis FIFO untuk pesanan, status cucian, pembayaran, dan reminder pengambilan.</p>
            </div>
            <div>
                <strong>Menu</strong>
                <p><a href="{{ route('services.index') }}">Layanan</a><br><a href="{{ route('status.form') }}">Cek Status</a><br><a href="{{ route('login') }}">Masuk</a></p>
            </div>
            <div>
                <strong>Kontak</strong>
                <p class="muted">WhatsApp: 0881012056484<br>Email: idoyrio37@gmail.com<br>Jam operasional: 08.00 - 20.00 WIB</p>
            </div>
        </div>
    </footer>
</body>
</html>

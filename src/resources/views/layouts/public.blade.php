<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LaundryKita - Sistem Manajemen Laundry')</title>
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
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
                <a href="{{ route('contact') }}">Kontak</a>
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

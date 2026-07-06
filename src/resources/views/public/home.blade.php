@extends('layouts.public')

@section('title', 'LaundryKita - Sistem Manajemen Laundry')

@section('content')
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="eyebrow">Laundry terpercaya untuk Anda</span>
                <h1>Bersih, Rapi, Wangi Setiap Saat</h1>
                <p class="lead">Pesan layanan laundry, pantau status cucian secara real-time, dan lihat riwayat pesanan dari satu sistem yang rapi.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ auth()->check() ? route('customer.orders.create') : route('register') }}">Buat Pesanan</a>
                    <a class="btn btn-outline" href="{{ route('status.form') }}">Cek Status Cucian</a>
                </div>
                <div class="trust-row">
                    <div class="trust-item"><span class="icon">A</span><div><strong>Aman</strong><br><span class="muted">Data pesanan tersimpan</span></div></div>
                    <div class="trust-item"><span class="icon">F</span><div><strong>FIFO</strong><br><span class="muted">Antrean sesuai urutan</span></div></div>
                    <div class="trust-item"><span class="icon">T</span><div><strong>Tracking</strong><br><span class="muted">Status cucian jelas</span></div></div>
                    <div class="trust-item"><span class="icon">R</span><div><strong>Reminder</strong><br><span class="muted">Pengingat pengambilan</span></div></div>
                </div>
            </div>
            <div class="hero-art">
                <img src="{{ asset('images/laundry-hero.png') }}" alt="Ilustrasi layanan laundry modern">
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Layanan Unggulan</h2>
                    <p>Pilih layanan sesuai kebutuhan, dari kiloan reguler sampai cuci sepatu, selimut, dan karpet.</p>
                </div>
                <a class="btn btn-outline" href="{{ route('services.index') }}">Lihat Semua</a>
            </div>
            <div class="service-grid">
                @foreach ($layananUnggulan as $layanan)
                    <article class="service-card">
                        <span class="icon">{{ strtoupper(substr($layanan->nama_layanan, 0, 1)) }}</span>
                        <div>
                            <h3>{{ $layanan->nama_layanan }}</h3>
                            <p class="muted">{{ \Illuminate\Support\Str::limit($layanan->deskripsi, 92) }}</p>
                        </div>
                        <div class="service-meta">
                            <span class="chip">{{ $layanan->nama_tipe_layanan }}</span>
                            <span class="chip">{{ $layanan->estimasi_hari }} hari</span>
                            <span class="chip">{{ $layanan->satuan_hitung }}</span>
                        </div>
                        <div class="price">Rp {{ number_format($layanan->tarif, 0, ',', '.') }}</div>
                        <a class="btn btn-outline" href="{{ route('services.show', $layanan) }}">Detail</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="cara-kerja" class="section" style="background:#fff;border-block:1px solid var(--line)">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Cara Kerja</h2>
                    <p>Alur dibuat singkat agar pelanggan dan admin sama-sama mudah memantau proses laundry.</p>
                </div>
            </div>
            <div class="steps">
                <div class="panel step"><span class="chip">1</span><h3>Daftar / Login</h3><p class="muted">Masuk untuk membuat pesanan dan melihat riwayat.</p></div>
                <div class="panel step"><span class="chip">2</span><h3>Buat Pesanan</h3><p class="muted">Pilih layanan, isi estimasi cucian, dan metode penyerahan.</p></div>
                <div class="panel step"><span class="chip">3</span><h3>Proses FIFO</h3><p class="muted">Admin memverifikasi lalu sistem mengatur antrean.</p></div>
                <div class="panel step"><span class="chip">4</span><h3>Pantau Status</h3><p class="muted">Status cucian dan pembayaran dapat dilihat kapan saja.</p></div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="panel status-box">
                <div>
                    <h2>Cek Status Cucian</h2>
                    <p class="muted">Masukkan nomor pesanan untuk melihat progres cucian tanpa membuka dashboard.</p>
                </div>
                <form method="POST" action="{{ route('status.check') }}" style="display:flex;gap:10px;flex-wrap:wrap">
                    @csrf
                    <input class="form-control" style="min-width:280px" name="nomor_pesanan" placeholder="Contoh: LDR-20260705-0001" required>
                    <button class="btn btn-primary" type="submit">Cek Status</button>
                </form>
            </div>
        </div>
    </section>
@endsection

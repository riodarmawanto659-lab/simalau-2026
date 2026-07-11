@extends('layouts.public')

@section('title', $layanan->nama_layanan . ' - LaundryKita')

@section('content')
    <section class="section">
        <div class="container">
            <div class="panel" style="display:grid;grid-template-columns:1fr 1fr;gap:26px;align-items:center">
                <div>
                    <img src="{{ $layanan->gambar_url }}" alt="Ilustrasi {{ $layanan->nama_layanan }}" style="border-radius:8px;border:1px solid var(--line)">
                </div>
                <div>
                    <span class="eyebrow">{{ $layanan->kategoriLayanan?->nama_kategori }}</span>
                    <h1>{{ $layanan->nama_layanan }}</h1>
                    <p class="lead">{{ $layanan->deskripsi }}</p>
                    <div class="service-meta">
                        <span class="chip">{{ $layanan->nama_tipe_layanan }}</span>
                        <span class="chip">Estimasi {{ $layanan->estimasi_hari }} hari</span>
                        <span class="chip">Minimal {{ $layanan->minimal_order ?: 1 }} {{ $layanan->satuan_hitung }}</span>
                    </div>
                    <p class="price">Rp {{ number_format($layanan->tarif, 0, ',', '.') }} / {{ $layanan->satuan_hitung }}</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="{{ auth()->check() ? route('customer.orders.create') : route('register') }}">Buat Pesanan</a>
                        <a class="btn btn-outline" href="{{ route('services.index') }}">Kembali ke Layanan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

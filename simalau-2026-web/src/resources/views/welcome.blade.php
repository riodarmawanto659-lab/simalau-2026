@extends('layouts.public')

@section('title', 'Simalau Laundry')

@section('content')
    <section class="section">
        <div class="container narrow" style="text-align:center">
            <span class="eyebrow">Simalau Laundry</span>
            <h1>Manajemen laundry siap jalan.</h1>
            <p class="muted">Aplikasi ini sudah diarahkan ke halaman utama, layanan, pesanan pelanggan, cek status, dan panel admin.</p>
            <div class="hero-actions" style="justify-content:center">
                <a class="btn btn-primary" href="{{ route('home') }}">Buka Beranda</a>
                <a class="btn btn-outline" href="{{ route('login') }}">Masuk Pelanggan</a>
                <a class="btn btn-dark" href="{{ route('filament.admin.auth.login') }}">Masuk Admin</a>
            </div>
        </div>
    </section>
@endsection

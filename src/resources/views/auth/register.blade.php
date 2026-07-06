@extends('layouts.public')

@section('title', 'Registrasi - LaundryKita')

@section('content')
    <section class="auth-wrap">
        <div class="container">
            <div class="auth-card">
                <div class="auth-visual">
                    <img src="{{ asset('images/laundry-hero.png') }}" alt="Ilustrasi laundry">
                    <div>
                        <h2>Buat Akun Baru</h2>
                        <p class="muted">Daftar untuk mulai membuat pesanan dan memantau status laundry Anda.</p>
                    </div>
                    <div class="trust-row" style="grid-template-columns:repeat(3,1fr);margin:0">
                        <div class="trust-item"><span class="icon">A</span><div><strong>Aman</strong></div></div>
                        <div class="trust-item"><span class="icon">C</span><div><strong>Cepat</strong></div></div>
                        <div class="trust-item"><span class="icon">N</span><div><strong>Notifikasi</strong></div></div>
                    </div>
                </div>
                <div class="auth-form">
                    <h1 style="font-size:34px">Daftar Akun</h1>
                    <p class="muted">Lengkapi data untuk membuat akun pelanggan.</p>
                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf
                        <div class="field">
                            <label for="name">Nama Lengkap</label>
                            <input id="name" class="form-control" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                            @error('email')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="nomor_whatsapp">No. WhatsApp</label>
                            <input id="nomor_whatsapp" class="form-control" name="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}" required>
                            @error('nomor_whatsapp')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" class="form-control" name="alamat" required>{{ old('alamat') }}</textarea>
                            @error('alamat')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" class="form-control" type="password" name="password" required>
                            @error('password')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">Daftar Sekarang</button>
                    </form>
                    <p class="muted" style="text-align:center;margin-top:18px">Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--primary);font-weight:800">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </section>
@endsection

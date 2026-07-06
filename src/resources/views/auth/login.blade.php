@extends('layouts.public')

@section('title', 'Masuk - LaundryKita')

@section('content')
    <section class="auth-wrap">
        <div class="container">
            <div class="auth-card">
                <div class="auth-visual">
                    <img src="{{ asset('images/laundry-hero.png') }}" alt="Ilustrasi laundry">
                    <div>
                        <h2>Selamat Datang Kembali</h2>
                        <p class="muted">Masuk untuk membuat pesanan, melihat status cucian, dan memantau pembayaran.</p>
                    </div>
                    <div class="trust-row" style="grid-template-columns:repeat(3,1fr);margin:0">
                        <div class="trust-item"><span class="icon">A</span><div><strong>Aman</strong></div></div>
                        <div class="trust-item"><span class="icon">F</span><div><strong>FIFO</strong></div></div>
                        <div class="trust-item"><span class="icon">R</span><div><strong>Reminder</strong></div></div>
                    </div>
                </div>
                <div class="auth-form">
                    <h1 style="font-size:34px">Masuk</h1>
                    <p class="muted">Gunakan email dan password Anda.</p>
                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                            @error('email')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" class="form-control" type="password" name="password" placeholder="Password Anda" required>
                            @error('password')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field" style="display:flex;justify-content:space-between;gap:12px;align-items:center">
                            <label style="margin:0;font-weight:650"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                            <a class="muted" href="/admin/password-reset/request">Lupa password?</a>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">Masuk</button>
                    </form>
                    <p class="muted" style="text-align:center;margin-top:18px">Belum punya akun? <a href="{{ route('register') }}" style="color:var(--primary);font-weight:800">Daftar sekarang</a></p>
                </div>
            </div>
        </div>
    </section>
@endsection

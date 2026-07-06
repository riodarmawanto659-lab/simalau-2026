@extends('layouts.public')

@section('title', 'Profil Saya - LaundryKita')

@section('content')
    <div class="container app-shell">
        @include('partials.customer-sidebar')
        <section class="content-stack">
            <div class="panel">
                <span class="eyebrow">Akun pelanggan</span>
                <h1>Profil Saya</h1>
                <p class="muted">Perbarui data kontak agar admin mudah menghubungi Anda.</p>
            </div>
            <div class="panel" style="max-width:760px">
                @if (session('success'))
                    <div class="alert">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('customer.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="field">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input id="nama_lengkap" class="form-control" name="nama_lengkap" value="{{ old('nama_lengkap', $pelanggan->nama_lengkap) }}" required>
                        @error('nama_lengkap')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input class="form-control" value="{{ $pelanggan->email }}" disabled>
                    </div>
                    <div class="field">
                        <label for="nomor_whatsapp">No. WhatsApp</label>
                        <input id="nomor_whatsapp" class="form-control" name="nomor_whatsapp" value="{{ old('nomor_whatsapp', $pelanggan->nomor_whatsapp) }}" required>
                        @error('nomor_whatsapp')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" class="form-control" name="alamat" required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                        @error('alamat')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                </form>
            </div>
        </section>
    </div>
@endsection

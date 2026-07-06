@extends('layouts.public')

@section('title', 'Cek Status Cucian - LaundryKita')

@section('content')
    <section class="section">
        <div class="container">
            <div class="panel" style="max-width:760px;margin:auto">
                <span class="eyebrow">Tracking pesanan</span>
                <h1>Cek Status Cucian</h1>
                <p class="lead">Gunakan nomor pesanan untuk melihat status cucian dan pembayaran.</p>
                <form method="POST" action="{{ route('status.check') }}">
                    @csrf
                    <div class="field">
                        <label for="nomor_pesanan">Nomor Pesanan</label>
                        <input id="nomor_pesanan" class="form-control" name="nomor_pesanan" value="{{ old('nomor_pesanan') }}" placeholder="LDR-YYYYMMDD-XXXX" required>
                        @error('nomor_pesanan')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary" type="submit">Cek Status</button>
                </form>
            </div>
        </div>
    </section>
@endsection

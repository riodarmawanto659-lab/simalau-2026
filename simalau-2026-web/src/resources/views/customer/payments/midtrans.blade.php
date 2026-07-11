@extends('layouts.public')

@section('title', 'Pembayaran Midtrans - LaundryKita')

@section('content')
    <div class="container app-shell">
        @include('partials.customer-sidebar')
        <section class="content-stack">
            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert" style="border-color:#fecaca;background:#fef2f2;color:#991b1b">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="panel">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Midtrans Sandbox</span>
                        <h1>Pembayaran Midtrans</h1>
                        <p class="muted">Lanjutkan pembayaran pesanan {{ $pembayaran->pesanan->nomor_pesanan }} melalui Midtrans Sandbox.</p>
                    </div>
                    <a class="btn btn-outline" href="{{ route('customer.orders.show', $pembayaran->pesanan) }}">Kembali</a>
                </div>

                <div class="stats-grid" style="margin-bottom:18px">
                    <div class="stat-card">
                        <span class="muted">Nomor Pesanan</span>
                        <strong style="font-size:18px;line-height:1.25">{{ $pembayaran->pesanan->nomor_pesanan }}</strong>
                    </div>
                    <div class="stat-card">
                        <span class="muted">Nomor Pembayaran</span>
                        <strong style="font-size:18px;line-height:1.25">{{ $pembayaran->nomor_pembayaran }}</strong>
                    </div>
                    <div class="stat-card">
                        <span class="muted">Total Tagihan</span>
                        <strong style="font-size:20px">Rp {{ number_format((float) $pembayaran->total_tagihan, 0, ',', '.') }}</strong>
                    </div>
                    <div class="stat-card">
                        <span class="muted">Status</span>
                        <strong style="font-size:18px">{{ $pembayaran->nama_status_pembayaran }}</strong>
                    </div>
                </div>

                <div class="order-form-grid" style="grid-template-columns:minmax(0,1fr) 320px;margin-bottom:0">
                    <div>
                        <h3>Instruksi Pembayaran</h3>
                        <p class="muted">Klik tombol bayar untuk membuka popup pembayaran Midtrans Sandbox. Setelah pembayaran berhasil, status pembayaran akan dikonfirmasi otomatis melalui webhook Midtrans.</p>
                        <p class="muted" style="margin-bottom:0">Jika popup tertutup sebelum selesai, Anda bisa kembali ke detail pesanan dan klik tombol pembayaran lagi.</p>
                    </div>
                    <div class="stat-card" style="text-align:center">
                        <button id="pay-button" class="btn btn-primary btn-block" type="button">Bayar Sekarang</button>
                        <a class="btn btn-outline btn-block" style="margin-top:10px" href="{{ route('customer.orders.show', $pembayaran->pesanan) }}">Kembali ke Detail Pesanan</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            if (typeof snap === 'undefined') {
                alert('Midtrans Snap belum berhasil dimuat. Cek MIDTRANS_CLIENT_KEY dan koneksi internet.');
                return;
            }

            snap.pay('{{ $pembayaran->snap_token }}', {
                onSuccess: function () {
                    window.location.href = "{{ route('customer.orders.show', $pembayaran->pesanan) }}";
                },
                onPending: function () {
                    window.location.href = "{{ route('customer.orders.show', $pembayaran->pesanan) }}";
                },
                onError: function () {
                    alert('Pembayaran gagal diproses oleh Midtrans.');
                },
                onClose: function () {
                    alert('Popup pembayaran ditutup sebelum pembayaran selesai.');
                }
            });
        });
    </script>
@endsection

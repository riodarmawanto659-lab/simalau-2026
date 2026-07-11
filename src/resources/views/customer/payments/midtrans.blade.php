@extends('layouts.public')

@section('title', 'Pembayaran Midtrans - LaundryKita')

@section('content')
    <div class="container app-shell">
        @include('partials.customer-sidebar')

        <section class="content-stack">
            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            @if (session('info'))
                <div class="alert" style="border-color:#fef3c7;background:#fffbeb;color:#92400e">{{ session('info') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert" style="border-color:#fecaca;background:#fef2f2;color:#991b1b">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="panel">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">{{ $pembayaran->pesanan->nomor_pesanan }}</span>
                        <h1>Pembayaran Midtrans Sandbox</h1>
                        <p class="muted">
                            Lanjutkan pembayaran untuk pesanan {{ $pembayaran->pesanan->nomor_pesanan }} melalui Midtrans Sandbox.
                        </p>
                    </div>

                    <a class="btn btn-outline" href="{{ route('customer.orders.show', $pembayaran->pesanan) }}">
                        Kembali
                    </a>
                </div>

                <div class="stats-grid" style="margin-bottom:18px">
                    <div class="stat-card">
                        <span class="muted">Nomor Pembayaran</span>
                        <strong style="font-size:18px;line-height:1.25">
                            {{ $pembayaran->nomor_pembayaran }}
                        </strong>
                    </div>

                    <div class="stat-card">
                        <span class="muted">Metode</span>
                        <strong style="font-size:18px">
                            {{ $pembayaran->nama_metode_pembayaran }}
                        </strong>
                    </div>

                    <div class="stat-card">
                        <span class="muted">Total Tagihan</span>
                        <strong style="font-size:20px">
                            Rp {{ number_format((float) $pembayaran->total_tagihan, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="stat-card">
                        <span class="muted">Status</span>
                        <strong style="font-size:18px">
                            {{ $pembayaran->nama_status_pembayaran }}
                        </strong>
                    </div>
                </div>

                <div class="order-form-grid" style="grid-template-columns:minmax(0,1fr) 320px;margin-bottom:0">
                    <div>
                        <h3>Instruksi Pembayaran</h3>
                        <p class="muted">
                            Klik tombol <strong>Bayar Sekarang</strong> untuk membuka popup pembayaran Midtrans Sandbox.
                            Anda bisa memilih metode pembayaran yang tersedia (QRIS, GoPay, Dana, Bank Transfer, dll).
                        </p>
                        <p class="muted">
                            Jika ingin mengganti metode pembayaran, cukup tutup popup lalu klik tombol Bayar Sekarang lagi.
                        </p>
                        <p class="muted" style="margin-bottom:0">
                            Setelah pembayaran berhasil, status akan berubah menjadi <strong>Lunas</strong> secara otomatis.
                        </p>
                    </div>

                    <div class="stat-card" style="text-align:center;padding:24px">
                        <button
                            id="pay-button"
                            class="btn btn-primary btn-block"
                            type="button"
                            style="display:block;width:100%;text-align:center;font-size:16px;padding:14px 0"
                        >
                            Bayar Sekarang
                        </button>

                        <a
                            class="btn btn-outline btn-block"
                            style="display:block;width:100%;text-align:center;margin-top:12px"
                            href="{{ route('customer.orders.show', $pembayaran->pesanan) }}"
                        >
                            Kembali ke Detail Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            if (typeof snap === 'undefined') {
                alert('Midtrans Snap belum berhasil dimuat. Periksa koneksi internet dan coba refresh halaman.');
                return;
            }

            snap.pay('{{ $pembayaran->snap_token }}', {
                onSuccess: function () {
                    window.location.href = "{{ route('customer.payments.midtrans.callback', $pembayaran) }}?transaction_status=settlement&payment_type=qris&fraud_status=accept";
                },
                onPending: function () {
                    window.location.href = "{{ route('customer.payments.midtrans.callback', $pembayaran) }}?transaction_status=pending";
                },
                onError: function () {
                    alert('Pembayaran gagal diproses oleh Midtrans. Silakan coba lagi.');
                },
                onClose: function () {
                    // Popup ditutup — user bisa klik tombol lagi untuk ganti metode
                }
            });
        });
    </script>
@endsection
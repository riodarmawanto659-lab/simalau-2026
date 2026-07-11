@extends('layouts.app')

@section('content')
    <main class="min-h-screen bg-slate-50 py-12">
        <div class="mx-auto max-w-2xl px-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h1 class="text-3xl font-bold text-slate-900">Pembayaran Midtrans</h1>

                <p class="mt-3 text-slate-600">
                    Silakan lanjutkan pembayaran untuk pesanan
                    <strong>{{ $pembayaran->pesanan->nomor_pesanan }}</strong>.
                </p>

                <div class="mt-6 rounded-2xl bg-slate-50 p-5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Tagihan</span>
                        <strong class="text-slate-900">
                            Rp {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="mt-3 flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <strong class="text-slate-900">
                            {{ $pembayaran->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Dibayar' }}
                        </strong>
                    </div>
                </div>

                <button
                    id="pay-button"
                    class="mt-6 w-full rounded-2xl bg-blue-600 px-6 py-4 font-semibold text-white hover:bg-blue-700">
                    Bayar Sekarang
                </button>

                <a href="{{ route('dashboard.pesanan.show', $pembayaran->pesanan_id) }}"
                   class="mt-4 inline-flex w-full justify-center rounded-2xl border border-slate-200 px-6 py-4 font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali ke Detail Pesanan
                </a>
            </div>
        </div>
    </main>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ $clientKey }}"></script>

    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            snap.pay('{{ $pembayaran->snap_token }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('dashboard.pesanan.show', $pembayaran->pesanan_id) }}";
                },
                onPending: function(result) {
                    window.location.href = "{{ route('dashboard.pesanan.show', $pembayaran->pesanan_id) }}";
                },
                onError: function(result) {
                    alert('Pembayaran gagal diproses.');
                },
                onClose: function() {
                    alert('Popup pembayaran ditutup sebelum pembayaran selesai.');
                }
            });
        });
    </script>
@endsection

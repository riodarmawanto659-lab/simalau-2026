@extends('layouts.public')

@section('title', 'Dashboard Pelanggan - LaundryKita')

@section('content')
    <div class="container app-shell">
        @include('partials.customer-sidebar')
        <section class="content-stack">
            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif
            <div class="panel">
                <span class="eyebrow">Dashboard pelanggan</span>
                <h1>Halo, {{ $pelanggan->nama_lengkap }}</h1>
                <p class="muted">Pantau aktivitas laundry, status cucian, dan pembayaran terbaru Anda.</p>
            </div>
            <div class="stats-grid">
                <div class="stat-card"><span class="muted">Total Pesanan</span><strong>{{ $totalPesanan }}</strong></div>
                <div class="stat-card"><span class="muted">Pesanan Aktif</span><strong>{{ $pesananAktif }}</strong></div>
                <div class="stat-card"><span class="muted">Siap Diambil</span><strong>{{ $siapDiambil }}</strong></div>
                <div class="stat-card"><span class="muted">Perlu Pembayaran</span><strong>{{ $belumDibayar }}</strong></div>
            </div>
            <div class="panel">
                <div class="section-head">
                    <div>
                        <h2>Pesanan Terbaru</h2>
                        <p>Daftar ringkas pesanan yang baru dibuat atau diperbarui.</p>
                    </div>
                    <a class="btn btn-primary" href="{{ route('customer.orders.create') }}">Buat Pesanan</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Tanggal</th>
                                <th>Status Cucian</th>
                                <th>Pembayaran</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pesananTerbaru as $pesanan)
                                <tr>
                                    <td><strong>{{ $pesanan->nomor_pesanan }}</strong></td>
                                    <td>{{ $pesanan->tanggal_masuk?->format('d M Y, H:i') }}</td>
                                    <td>@include('partials.status-badge', ['status' => $pesanan->status_pesanan, 'label' => $pesanan->nama_status_pesanan])</td>
                                    <td>@include('partials.status-badge', ['status' => $pesanan->status_pembayaran, 'label' => $pesanan->nama_status_pembayaran])</td>
                                    <td>{{ $pesanan->total_biaya_rupiah }}</td>
                                    <td>
                                        <a class="btn btn-outline" href="{{ route('customer.orders.show', $pesanan) }}{{ $pesanan->status_pembayaran === 'lunas' ? '' : '#pembayaran' }}">
                                            {{ match ($pesanan->status_pembayaran) {
                                                'lunas' => 'Detail',
                                                'menunggu_konfirmasi' => 'Cek Bukti',
                                                default => 'Bayar',
                                            } }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">Belum ada pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

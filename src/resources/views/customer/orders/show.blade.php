@extends('layouts.public')

@section('title', $pesanan->nomor_pesanan . ' - LaundryKita')

@section('content')
    <div class="container app-shell">
        @include('partials.customer-sidebar')
        <section class="content-stack">
            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif
            <div class="panel">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">{{ $pesanan->nomor_pesanan }}</span>
                        <h1>Detail Pesanan</h1>
                        <p class="muted">Tanggal masuk: {{ $pesanan->tanggal_masuk?->format('d M Y, H:i') }}</p>
                    </div>
                    <a class="btn btn-outline" href="{{ route('customer.orders.index') }}">Kembali</a>
                </div>
                <div class="stats-grid">
                    <div class="stat-card"><span class="muted">Status Cucian</span><br>@include('partials.status-badge', ['status' => $pesanan->status_pesanan, 'label' => $pesanan->nama_status_pesanan])</div>
                    <div class="stat-card"><span class="muted">Pembayaran</span><br>@include('partials.status-badge', ['status' => $pesanan->status_pembayaran, 'label' => $pesanan->nama_status_pembayaran])</div>
                    <div class="stat-card"><span class="muted">Estimasi Selesai</span><strong style="font-size:20px">{{ $pesanan->estimasi_selesai?->format('d M Y') ?? '-' }}</strong></div>
                    <div class="stat-card"><span class="muted">Total</span><strong style="font-size:20px">{{ $pesanan->total_biaya_rupiah }}</strong></div>
                </div>
            </div>
            <div class="order-form-grid">
                <div class="panel">
                    <h2>Status Cucian</h2>
                    <div class="timeline">
                        @php
                            $currentIndex = array_search($pesanan->status_pesanan, \App\Models\Pesanan::STATUS_FLOW, true);
                            $currentIndex = $currentIndex === false ? -1 : $currentIndex;
                        @endphp
                        @foreach (\App\Models\Pesanan::STATUS_FLOW as $index => $status)
                            @php $history = $pesanan->riwayatStatuses->firstWhere('status_baru', $status); @endphp
                            <div class="timeline-item {{ $index <= $currentIndex ? 'done' : '' }}">
                                <span class="timeline-dot"></span>
                                <div>
                                    <strong>{{ \App\Models\Pesanan::STATUS_LABELS[$status] }}</strong>
                                    <div class="muted">{{ $history?->tanggal_perubahan?->format('d M Y, H:i') ?? 'Belum masuk tahap ini' }}</div>
                                    @if ($history?->catatan)
                                        <div>{{ $history->catatan }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="panel">
                    <h2>Rincian Pesanan</h2>
                    @foreach ($pesanan->detailPesanans as $detail)
                        <div style="border-bottom:1px solid var(--line);padding:12px 0">
                            <strong>{{ $detail->nama_layanan }}</strong>
                            <p class="muted">{{ $detail->jumlah_display }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                            <div>{{ $detail->subtotal_rupiah }}</div>
                        </div>
                    @endforeach
                    <div style="display:grid;gap:8px;margin-top:16px">
                        <div><span class="muted">Metode Penyerahan</span><br><strong>{{ $pesanan->nama_metode_penyerahan }}</strong></div>
                        @if ($pesanan->alamat_penjemputan)
                            <div><span class="muted">Alamat Jemput</span><br>{{ $pesanan->alamat_penjemputan }}</div>
                        @endif
                        <div><span class="muted">Catatan</span><br>{{ $pesanan->catatan_pelanggan ?: '-' }}</div>
                    </div>
                    <hr style="border:0;border-top:1px solid var(--line);margin:18px 0">
                    <div style="display:flex;justify-content:space-between;gap:12px"><span>Total Pembayaran</span><strong>{{ $pesanan->total_biaya_rupiah }}</strong></div>
                </div>
            </div>
        </section>
    </div>
@endsection

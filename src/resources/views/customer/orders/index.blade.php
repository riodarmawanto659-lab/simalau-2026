@extends('layouts.public')

@section('title', 'Pesanan Saya - LaundryKita')

@section('content')
    <div class="container app-shell">
        @include('partials.customer-sidebar')
        <section class="content-stack">
            <div class="panel">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Riwayat pesanan</span>
                        <h1>Pesanan Saya</h1>
                        <p class="muted">Seluruh pesanan laundry yang pernah Anda buat.</p>
                    </div>
                    <a class="btn btn-primary" href="{{ route('customer.orders.create') }}">Buat Pesanan</a>
                </div>
                <div class="service-meta" style="margin-bottom:18px">
                    <a class="chip" href="{{ route('customer.orders.index') }}">Semua</a>
                    @foreach (\App\Models\Pesanan::STATUS_LABELS as $key => $label)
                        <a class="chip" href="{{ route('customer.orders.index', ['status' => $key]) }}">{{ $label }}</a>
                    @endforeach
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Tanggal</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status Cucian</th>
                                <th>Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pesanans as $pesanan)
                                <tr>
                                    <td><strong>{{ $pesanan->nomor_pesanan }}</strong></td>
                                    <td>{{ $pesanan->tanggal_masuk?->format('d M Y, H:i') }}</td>
                                    <td>{{ $pesanan->detailPesanans->pluck('nama_layanan')->join(', ') ?: '-' }}</td>
                                    <td>{{ $pesanan->total_biaya_rupiah }}</td>
                                    <td>@include('partials.status-badge', ['status' => $pesanan->status_pesanan, 'label' => $pesanan->nama_status_pesanan])</td>
                                    <td>@include('partials.status-badge', ['status' => $pesanan->status_pembayaran, 'label' => $pesanan->nama_status_pembayaran])</td>
                                    <td><a class="btn btn-outline" href="{{ route('customer.orders.show', $pesanan) }}">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7">Belum ada pesanan untuk filter ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:18px">{{ $pesanans->links() }}</div>
            </div>
        </section>
    </div>
@endsection

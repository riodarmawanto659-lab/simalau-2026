@extends('layouts.public')

@section('title', 'Status ' . $pesanan->nomor_pesanan . ' - LaundryKita')

@section('content')
    <section class="section">
        <div class="container">
            <div class="panel">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">{{ $pesanan->nomor_pesanan }}</span>
                        <h1>Status Cucian</h1>
                        <p class="lead">{{ $pesanan->pelanggan?->nama_lengkap }} - estimasi selesai {{ $pesanan->estimasi_selesai?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        @include('partials.status-badge', ['status' => $pesanan->status_pesanan, 'label' => $pesanan->nama_status_pesanan])
                        @include('partials.status-badge', ['status' => $pesanan->status_pembayaran, 'label' => $pesanan->nama_status_pembayaran])
                    </div>
                </div>
                <div class="timeline">
                    @php
                        $currentIndex = array_search($pesanan->status_pesanan, \App\Models\Pesanan::STATUS_FLOW, true);
                        $currentIndex = $currentIndex === false ? -1 : $currentIndex;
                    @endphp
                    @foreach (\App\Models\Pesanan::STATUS_FLOW as $status)
                        @php
                            $done = array_search($status, \App\Models\Pesanan::STATUS_FLOW, true) <= $currentIndex;
                            $history = $pesanan->riwayatStatuses->firstWhere('status_baru', $status);
                        @endphp
                        <div class="timeline-item {{ $done ? 'done' : '' }}">
                            <span class="timeline-dot"></span>
                            <div>
                                <strong>{{ \App\Models\Pesanan::STATUS_LABELS[$status] }}</strong>
                                <div class="muted">{{ $history?->tanggal_perubahan?->format('d M Y, H:i') ?? 'Belum masuk tahap ini' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

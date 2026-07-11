@extends('layouts.public')

@section('title', $pesanan->nomor_pesanan . ' - LaundryKita')

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
                        <span class="eyebrow">{{ $pesanan->nomor_pesanan }}</span>
                        <h1>Detail Pesanan</h1>
                        <p class="muted">
                            Tanggal masuk: {{ $pesanan->tanggal_masuk?->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <a class="btn btn-outline" href="{{ route('customer.orders.index') }}">
                        Kembali
                    </a>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="muted">Status Cucian</span><br>
                        @include('partials.status-badge', [
                            'status' => $pesanan->status_pesanan,
                            'label' => $pesanan->nama_status_pesanan,
                        ])
                    </div>

                    <div class="stat-card">
                        <span class="muted">Pembayaran</span><br>
                        @include('partials.status-badge', [
                            'status' => $pesanan->status_pembayaran,
                            'label' => $pesanan->nama_status_pembayaran,
                        ])
                    </div>

                    <div class="stat-card">
                        <span class="muted">Estimasi Selesai</span>
                        <strong style="font-size:20px">
                            {{ $pesanan->estimasi_selesai?->format('d M Y') ?? '-' }}
                        </strong>
                    </div>

                    <div class="stat-card">
                        <span class="muted">Total</span>
                        <strong style="font-size:20px">
                            {{ $pesanan->total_biaya_rupiah }}
                        </strong>
                    </div>
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
                            @php
                                $history = $pesanan->riwayatStatuses->firstWhere('status_baru', $status);
                            @endphp

                            <div class="timeline-item {{ $index <= $currentIndex ? 'done' : '' }}">
                                <span class="timeline-dot"></span>

                                <div>
                                    <strong>{{ \App\Models\Pesanan::STATUS_LABELS[$status] }}</strong>

                                    <div class="muted">
                                        {{ $history?->tanggal_perubahan?->format('d M Y, H:i') ?? 'Belum masuk tahap ini' }}
                                    </div>

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
                        <div style="border-bottom:1px solid var(--line);padding:12px 0;display:flex;gap:12px;align-items:flex-start">
                            @if ($detail->gambar_url)
                                <img
                                    src="{{ $detail->gambar_url }}"
                                    alt="{{ $detail->nama_layanan }}"
                                    style="width:82px;height:82px;object-fit:cover;border-radius:8px;border:1px solid var(--line);flex:0 0 auto"
                                >
                            @endif

                            <div>
                                <strong>{{ $detail->nama_layanan }}</strong>

                                <p class="muted">
                                    {{ $detail->jumlah_display }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                </p>

                                <div>{{ $detail->subtotal_rupiah }}</div>
                            </div>
                        </div>
                    @endforeach

                    <div style="display:grid;gap:8px;margin-top:16px">
                        <div>
                            <span class="muted">Metode Penyerahan</span><br>
                            <strong>{{ $pesanan->nama_metode_penyerahan }}</strong>
                        </div>

                        @if ($pesanan->alamat_penjemputan)
                            <div>
                                <span class="muted">Alamat Jemput</span><br>
                                {{ $pesanan->alamat_penjemputan }}
                            </div>
                        @endif

                        <div>
                            <span class="muted">Catatan</span><br>
                            {{ $pesanan->catatan_pelanggan ?: '-' }}
                        </div>
                    </div>

                    <hr style="border:0;border-top:1px solid var(--line);margin:18px 0">

                    <div style="display:flex;justify-content:space-between;gap:12px">
                        <span>Total Pembayaran</span>
                        <strong>{{ $pesanan->total_biaya_rupiah }}</strong>
                    </div>
                </div>
            </div>

            <div id="pembayaran" class="panel">
                <div class="section-head">
                    <div>
                        <h2>Pembayaran</h2>
                        <p>
                            Pembayaran dapat dilakukan secara otomatis melalui Midtrans tanpa perlu upload bukti pembayaran.
                        </p>
                    </div>

                    @include('partials.status-badge', [
                        'status' => $pesanan->status_pembayaran,
                        'label' => $pesanan->nama_status_pembayaran,
                    ])
                </div>

                @if ($pesanan->pembayaran)
                    <div class="stats-grid" style="margin-bottom:18px">
                        <div class="stat-card">
                            <span class="muted">Nomor Pembayaran</span>
                            <strong style="font-size:18px;line-height:1.25">
                                {{ $pesanan->pembayaran->nomor_pembayaran }}
                            </strong>
                        </div>

                        <div class="stat-card">
                            <span class="muted">Metode</span>
                            <strong style="font-size:18px">
                                {{ $pesanan->pembayaran->nama_metode_pembayaran }}
                            </strong>
                        </div>

                        <div class="stat-card">
                            <span class="muted">Total Tagihan</span>
                            <strong style="font-size:20px">
                                Rp {{ number_format((float) $pesanan->pembayaran->total_tagihan, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="stat-card">
                            <span class="muted">Tanggal</span>
                            <strong style="font-size:18px">
                                {{ $pesanan->tanggal_masuk?->format('d M Y, H:i') ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    @if ($pesanan->status_pembayaran === 'lunas')
                        <div class="stats-grid" style="margin-bottom:18px">
                            <div class="stat-card">
                                <span class="muted">Status</span>
                                <strong style="font-size:18px;color:#16a34a">✓ LUNAS</strong>
                            </div>

                            <div class="stat-card">
                                <span class="muted">Nominal Dibayar</span>
                                <strong style="font-size:20px">
                                    Rp {{ number_format((float) $pesanan->pembayaran->nominal_dibayar, 0, ',', '.') }}
                                </strong>
                            </div>

                            <div class="stat-card">
                                <span class="muted">Tanggal Bayar</span>
                                <strong style="font-size:18px">
                                    {{ $pesanan->pembayaran->tanggal_pembayaran?->format('d M Y, H:i') ?? '-' }}
                                </strong>
                            </div>
                        </div>
                    @endif
                @endif

                @if ($pesanan->status_pembayaran !== 'lunas')
                    <div class="order-form-grid" style="grid-template-columns:minmax(0,1fr) 320px;margin-bottom:0">
                        <div>
                            <h3>Bayar Otomatis dengan Midtrans</h3>

                            <p class="muted">
                                Klik tombol <strong>Bayar Sekarang</strong> untuk melakukan pembayaran otomatis melalui Midtrans.
                                Pembayaran akan diverifikasi secara otomatis tanpa perlu upload bukti.
                            </p>

                            <p class="muted" style="margin-bottom:0">
                                Total tagihan: <strong>{{ $pesanan->total_biaya_rupiah }}</strong>
                            </p>
                        </div>

                        <div class="stat-card" style="text-align:center;padding:24px">
                            @if ($pesanan->pembayaran)
                                <a
                                    class="btn btn-primary btn-block"
                                    href="{{ route('customer.payments.midtrans', $pesanan->pembayaran) }}"
                                    style="display:block;width:100%;text-align:center;font-size:16px;padding:14px 0"
                                >
                                    Bayar dengan Midtrans
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="order-form-grid" style="grid-template-columns:minmax(0,1fr) 320px;margin-bottom:0">
                        <div>
                            <h3>Pembayaran Berhasil</h3>
                            <p class="muted" style="margin-bottom:0">
                                Pembayaran sebesar
                                <strong>Rp {{ number_format((float) $pesanan->pembayaran->nominal_dibayar, 0, ',', '.') }}</strong>
                                telah diterima dan pesanan sudah otomatis diproses.
                            </p>
                        </div>

                        <div class="stat-card" style="text-align:center;padding:24px">
                            <span style="font-size:48px">✅</span>
                            <p class="muted" style="margin:8px 0 0">
                                Status: <strong style="color:#16a34a">LUNAS</strong>
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
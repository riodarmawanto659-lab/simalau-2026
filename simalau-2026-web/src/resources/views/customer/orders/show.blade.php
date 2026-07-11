@extends('layouts.public')

@section('title', $pesanan->nomor_pesanan . ' - LaundryKita')

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
                        <div style="border-bottom:1px solid var(--line);padding:12px 0;display:flex;gap:12px;align-items:flex-start">
                            @if ($detail->gambar_url)
                                <img src="{{ $detail->gambar_url }}" alt="{{ $detail->nama_layanan }}" style="width:82px;height:82px;object-fit:cover;border-radius:8px;border:1px solid var(--line);flex:0 0 auto">
                            @endif
                            <div>
                                <strong>{{ $detail->nama_layanan }}</strong>
                                <p class="muted">{{ $detail->jumlah_display }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                                <div>{{ $detail->subtotal_rupiah }}</div>
                            </div>
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
            <div id="pembayaran" class="panel">
                <div class="section-head">
                    <div>
                        <h2>Pembayaran</h2>
                        <p>Scan QRIS outlet yang otomatis tersedia untuk pesanan ini, lalu upload bukti pembayaran agar admin dapat mengecek dan mengonfirmasi pesanan.</p>
                    </div>
                    @include('partials.status-badge', ['status' => $pesanan->status_pembayaran, 'label' => $pesanan->nama_status_pembayaran])
                </div>

                @if ($pesanan->pembayaran)
                    <div class="stats-grid" style="margin-bottom:18px">
                        <div class="stat-card">
                            <span class="muted">Nomor Pembayaran</span>
                            <strong style="font-size:18px;line-height:1.25">{{ $pesanan->pembayaran->nomor_pembayaran }}</strong>
                        </div>
                        <div class="stat-card">
                            <span class="muted">Metode</span>
                            <strong style="font-size:18px">{{ $pesanan->pembayaran->nama_metode_pembayaran }}</strong>
                        </div>
                        <div class="stat-card">
                            <span class="muted">Dibayar</span>
                            <strong style="font-size:18px">Rp {{ number_format((float) $pesanan->pembayaran->nominal_dibayar, 0, ',', '.') }}</strong>
                        </div>
                        <div class="stat-card">
                            <span class="muted">Tanggal</span>
                            <strong style="font-size:18px">{{ $pesanan->pembayaran->tanggal_pembayaran?->format('d M Y, H:i') ?? '-' }}</strong>
                        </div>
                    </div>
                    @if ($pesanan->pembayaran->bukti_pembayaran_url)
                        <p style="margin-top:0">
                            <a class="btn btn-outline" href="{{ $pesanan->pembayaran->bukti_pembayaran_url }}" target="_blank" rel="noopener">Lihat Bukti Pembayaran</a>
                        </p>
                    @endif
                @endif

                @if ($pesanan->status_pembayaran !== 'lunas')
                    <div class="order-form-grid" style="grid-template-columns:minmax(0,1fr) 320px;margin-bottom:18px">
                        <div>
                            <h3>Instruksi Pembayaran QRIS</h3>
                            <p class="muted">Pastikan nominal pembayaran sesuai total tagihan: <strong>{{ $pesanan->total_biaya_rupiah }}</strong>. QRIS ini dipakai otomatis untuk semua pesanan, jadi Anda bisa langsung scan setelah pesanan dibuat.</p>
                        </div>
                        <div class="stat-card" style="text-align:center">
                            @if (($pengaturanSistem ?? null)?->qris_image_url)
                                <img src="{{ $pengaturanSistem->qris_image_url }}" alt="QRIS {{ $pengaturanSistem->nama_laundry }}" style="width:100%;aspect-ratio:1/1;object-fit:contain;border:1px solid var(--line);border-radius:8px;background:white">
                            @else
                                <div style="min-height:220px;display:grid;place-items:center;border:1px dashed var(--line);border-radius:8px;background:#f8fafc;padding:18px">
                                    <div>
                                        <strong>QRIS belum tersedia</strong>
                                        <p class="muted" style="margin:8px 0 0">Admin cukup upload QRIS outlet satu kali dari menu Pengaturan Sistem.</p>
                                    </div>
                                </div>
                            @endif

                            @if ($pesanan->pembayaran && $pesanan->pembayaran->status_pembayaran !== 'lunas')
                                <div style="margin-top:14px;border-top:1px solid var(--line);padding-top:14px">
                                    <a class="btn btn-primary btn-block" href="{{ route('customer.payments.midtrans', $pesanan->pembayaran) }}">Bayar dengan Midtrans</a>
                                    <p class="muted" style="font-size:12px;margin:8px 0 0">Pembayaran online memakai Midtrans Sandbox.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('customer.orders.pay', $pesanan) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="field">
                            <label for="bukti_pembayaran">Bukti Pembayaran QRIS</label>
                            <input id="bukti_pembayaran" class="form-control" type="file" name="bukti_pembayaran" accept="image/png,image/jpeg,image/webp,application/pdf" required>
                            <div class="muted" style="font-size:13px;margin-top:6px">Format JPG, PNG, WEBP, atau PDF. Maksimal 4 MB.</div>
                            @error('bukti_pembayaran')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="catatan">Catatan Pembayaran</label>
                            <textarea id="catatan" class="form-control" name="catatan" maxlength="500" placeholder="Contoh: pembayaran QRIS atas nama pelanggan.">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">{{ $pesanan->status_pembayaran === 'menunggu_konfirmasi' ? 'Upload Ulang Bukti' : 'Kirim Bukti Pembayaran' }}</button>
                    </form>
                @else
                    <p class="muted" style="margin:0">Pembayaran pesanan ini sudah dikonfirmasi admin. Pesanan dapat masuk proses laundry berikutnya.</p>
                @endif
            </div>
        </section>
    </div>
@endsection

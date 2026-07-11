@extends('layouts.public')

@section('title', 'Layanan Laundry - LaundryKita')

@section('content')
    <section class="section" style="background:#fff;border-bottom:1px solid var(--line)">
        <div class="container">
            <span class="eyebrow">Daftar layanan</span>
            <h1>Layanan Laundry</h1>
            <p class="lead">Tarif dan estimasi pengerjaan layanan aktif yang dapat dipilih pelanggan.</p>
        </div>
    </section>

    @foreach ($kategoriLayanans as $kategori)
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2>{{ $kategori->nama_kategori }}</h2>
                        <p>{{ $kategori->deskripsi }}</p>
                    </div>
                </div>
                <div class="service-grid">
                    @forelse ($kategori->layananLaundries as $layanan)
                        <article class="service-card">
                            @if ($layanan->gambar)
                                <img src="{{ $layanan->gambar_url }}" alt="{{ $layanan->nama_layanan }}" style="height:150px;width:100%;object-fit:cover;border-radius:8px;border:1px solid var(--line)">
                            @else
                                <span class="icon">{{ strtoupper(substr($layanan->nama_layanan, 0, 1)) }}</span>
                            @endif
                            <h3>{{ $layanan->nama_layanan }}</h3>
                            <p class="muted">{{ $layanan->deskripsi }}</p>
                            <div class="service-meta">
                                <span class="chip">{{ $layanan->nama_tipe_layanan }}</span>
                                <span class="chip">{{ $layanan->estimasi_hari }} hari</span>
                                <span class="chip">Minimal {{ $layanan->minimal_order ?: 1 }} {{ $layanan->satuan_hitung }}</span>
                            </div>
                            <div class="price">Rp {{ number_format($layanan->tarif, 0, ',', '.') }} / {{ $layanan->satuan_hitung }}</div>
                            <a class="btn btn-outline" href="{{ route('services.show', $layanan) }}">Lihat Detail</a>
                        </article>
                    @empty
                        <div class="panel">Belum ada layanan aktif pada kategori ini.</div>
                    @endforelse
                </div>
            </div>
        </section>
    @endforeach
@endsection

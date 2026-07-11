@extends('layouts.public')

@section('title', 'Kontak - LaundryKita')

@section('content')
    <section class="section" style="background:#fff;border-bottom:1px solid var(--line)">
        <div class="container">
            <span class="eyebrow">Kontak LaundryKita</span>
            <h1>Kontak dan Informasi Operasional</h1>
            <p class="lead">Halaman ini berisi informasi kontak, jam operasional, dan pemberitahuan hari libur agar pelanggan tidak salah jadwal.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="service-grid">
                <div class="panel">
                    <span class="icon">WA</span>
                    <h3>WhatsApp</h3>
                    <p class="muted">{{ $pengaturan?->nomor_whatsapp ?: '0881012056484' }}</p>
                    @php
                        $wa = preg_replace('/\D+/', '', $pengaturan?->nomor_whatsapp ?: '0881012056484');
                        if (str_starts_with($wa, '0')) { $wa = '62' . substr($wa, 1); }
                    @endphp
                    <a class="btn btn-outline" href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener">Chat WhatsApp</a>
                </div>

                <div class="panel">
                    <span class="icon">@</span>
                    <h3>Email</h3>
                    <p class="muted">{{ $pengaturan?->email ?: 'idoyrio37@gmail.com' }}</p>
                    <a class="btn btn-outline" href="mailto:{{ $pengaturan?->email ?: 'idoyrio37@gmail.com' }}">Kirim Email</a>
                </div>

                <div class="panel">
                    <span class="icon">JM</span>
                    <h3>Jam Operasional</h3>
                    <p class="muted">{{ $pengaturan?->jam_operasional ?: '08.00 - 20.00 WIB' }}</p>
                    <p class="muted">{{ $pengaturan?->alamat ?: 'Alamat laundry dapat diatur melalui admin.' }}</p>
                </div>
            </div>

            <div class="panel" style="margin-top:22px">
                <div class="section-head" style="margin-bottom:14px">
                    <div>
                        <h2>Status Hari Libur</h2>
                        <p>Informasi ini diambil dari menu admin Hari Libur.</p>
                    </div>
                </div>

                @if ($hariLiburAktif)
                    <div class="alert" style="border-color:#fde68a;background:#fffbeb;color:#92400e">
                        <strong>Sedang libur:</strong> {{ $hariLiburAktif->nama_hari_libur }} pada {{ $hariLiburAktif->periode_libur }}.
                        @if ($hariLiburAktif->keterangan)
                            <br>{{ $hariLiburAktif->keterangan }}
                        @endif
                    </div>
                @else
                    <div class="alert">
                        <strong>Hari ini operasional berjalan normal.</strong> Tidak ada hari libur aktif yang tercatat untuk hari ini.
                    </div>
                @endif

                <h3>Hari Libur Mendatang</h3>
                <div class="table-wrap" style="margin-top:12px">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Periode</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hariLiburMendatang as $libur)
                                <tr>
                                    <td><strong>{{ $libur->nama_hari_libur }}</strong></td>
                                    <td>{{ $libur->periode_libur }}</td>
                                    <td>{{ $libur->nama_jenis }}</td>
                                    <td>{{ $libur->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">Belum ada jadwal hari libur mendatang.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.public')

@section('title', 'Buat Pesanan - LaundryKita')

@section('content')
    <div class="container app-shell">
        @include('partials.customer-sidebar')
        <section class="content-stack">
            @if (! empty($hariLiburAktif))
                <div class="alert" style="border-color:#fde68a;background:#fffbeb;color:#92400e">
                    <strong>Informasi hari libur:</strong> {{ $hariLiburAktif->nama_hari_libur }} berlangsung pada {{ $hariLiburAktif->periode_libur }}. Pesanan tetap bisa dibuat, tetapi estimasi pengerjaan dapat disesuaikan admin.
                </div>
            @endif
            <div class="panel">
                <span class="eyebrow">Pesanan baru</span>
                <h1>Buat Pesanan Laundry</h1>
                <p class="muted">Pilih layanan, isi estimasi cucian, dan tentukan cara penyerahan.</p>
            </div>
            <form method="POST" action="{{ route('customer.orders.store') }}" class="order-form-grid" id="orderForm">
                @csrf
                <div class="content-stack">
                    <div class="panel">
                        <h2>1. Pilih Layanan</h2>
                        <div class="choice-grid" style="margin-top:16px">
                            @foreach ($layanans as $layanan)
                                <label class="choice-card">
                                    <input
                                        type="radio"
                                        name="layanan_laundry_id"
                                        value="{{ $layanan->id }}"
                                        data-name="{{ $layanan->nama_layanan }}"
                                        data-type="{{ $layanan->tipe_layanan }}"
                                        data-unit="{{ $layanan->satuan_hitung }}"
                                        data-price="{{ (float) $layanan->tarif }}"
                                        data-min="{{ $layanan->minimal_order ?: 1 }}"
                                        data-estimation="{{ $layanan->estimasi_hari }}"
                                        {{ old('layanan_laundry_id') == $layanan->id ? 'checked' : '' }}
                                    >
                                    @if ($layanan->gambar)
                                        <img src="{{ $layanan->gambar_url }}" alt="{{ $layanan->nama_layanan }}" style="height:96px;width:100%;object-fit:cover;border-radius:8px;border:1px solid var(--line);margin-bottom:10px">
                                    @endif
                                    <strong>{{ $layanan->nama_layanan }}</strong>
                                    <p class="muted">Rp {{ number_format($layanan->tarif, 0, ',', '.') }} / {{ $layanan->satuan_hitung }}</p>
                                </label>
                            @endforeach
                        </div>
                        @error('layanan_laundry_id')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="panel">
                        <h2>2. Detail Cucian</h2>
                        <div class="field" id="fieldBerat">
                            <label for="berat">Berat Estimasi (kg)</label>
                            <input id="berat" class="form-control" type="number" step="0.1" min="0.1" name="berat" value="{{ old('berat') }}">
                            @error('berat')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field" id="fieldJumlah">
                            <label for="jumlah_item">Jumlah Item</label>
                            <input id="jumlah_item" class="form-control" type="number" min="1" max="10" name="jumlah_item" value="{{ old('jumlah_item') }}">
                            @error('jumlah_item')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="catatan_pelanggan">Catatan Khusus</label>
                            <textarea id="catatan_pelanggan" class="form-control" name="catatan_pelanggan" placeholder="Contoh: noda minyak, jangan pakai pewangi pekat">{{ old('catatan_pelanggan') }}</textarea>
                            @error('catatan_pelanggan')<div class="error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="panel">
                        <h2>3. Metode Penyerahan</h2>
                        <div class="choice-grid" style="margin-top:16px">
                            <label class="choice-card">
                                <input type="radio" name="metode_penyerahan" value="antar_sendiri" {{ old('metode_penyerahan', 'antar_sendiri') === 'antar_sendiri' ? 'checked' : '' }}>
                                <strong>Antar Sendiri</strong>
                                <p class="muted">Anda mengantar cucian ke outlet.</p>
                            </label>
                            <label class="choice-card">
                                <input type="radio" name="metode_penyerahan" value="jemput" {{ old('metode_penyerahan') === 'jemput' ? 'checked' : '' }}>
                                <strong>Minta Dijemput</strong>
                                <p class="muted">Admin menggunakan alamat jemput.</p>
                            </label>
                        </div>
                        @error('metode_penyerahan')<div class="error">{{ $message }}</div>@enderror
                        <div class="field" id="fieldAlamat" style="margin-top:16px">
                            <label for="alamat_penjemputan">Alamat Penjemputan</label>
                            <textarea id="alamat_penjemputan" class="form-control" name="alamat_penjemputan">{{ old('alamat_penjemputan') }}</textarea>
                            @error('alamat_penjemputan')<div class="error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <aside class="panel" style="position:sticky;top:92px">
                    <h2>Ringkasan Pesanan</h2>
                    <p class="muted" id="summaryService">Pilih layanan terlebih dahulu.</p>
                    <div style="display:grid;gap:12px;margin:18px 0">
                        <div><span class="muted">Jumlah</span><br><strong id="summaryQty">-</strong></div>
                        <div><span class="muted">Estimasi</span><br><strong id="summaryEstimation">-</strong></div>
                        <div><span class="muted">Subtotal</span><br><strong id="summarySubtotal">Rp 0</strong></div>
                    </div>
                    <p class="muted">Harga final dapat disesuaikan admin setelah cucian diperiksa.</p>
                    <button class="btn btn-primary btn-block" type="submit">Buat Pesanan &amp; Lanjut Bayar</button>
                </aside>
            </form>
        </section>
    </div>
    <script>
        const form = document.getElementById('orderForm');
        const currency = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
        const selectedService = () => form.querySelector('input[name="layanan_laundry_id"]:checked');
        const selectedMethod = () => form.querySelector('input[name="metode_penyerahan"]:checked')?.value;
        const berat = document.getElementById('berat');
        const jumlah = document.getElementById('jumlah_item');
        const fieldBerat = document.getElementById('fieldBerat');
        const fieldJumlah = document.getElementById('fieldJumlah');
        const fieldAlamat = document.getElementById('fieldAlamat');

        function refreshSummary() {
            const service = selectedService();
            fieldAlamat.style.display = selectedMethod() === 'jemput' ? 'block' : 'none';

            if (! service) return;

            const type = service.dataset.type;
            const unit = service.dataset.unit;
            const min = Number(service.dataset.min || 1);
            const price = Number(service.dataset.price || 0);
            const qtyInput = type === 'kiloan' ? berat : jumlah;
            const qty = Math.max(Number(qtyInput.value || min), min);

            fieldBerat.style.display = type === 'kiloan' ? 'block' : 'none';
            fieldJumlah.style.display = type === 'satuan' ? 'block' : 'none';
            document.getElementById('summaryService').textContent = service.dataset.name;
            document.getElementById('summaryQty').textContent = `${qty} ${unit}`;
            document.getElementById('summaryEstimation').textContent = `${service.dataset.estimation} hari`;
            document.getElementById('summarySubtotal').textContent = currency.format(qty * price);
        }

        form.addEventListener('input', refreshSummary);
        form.addEventListener('change', refreshSummary);
        refreshSummary();
    </script>
@endsection

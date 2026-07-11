<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use App\Models\LayananLaundry;
use App\Services\LaundryOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        return view('customer.orders.create', [
            'layanans' => LayananLaundry::query()
                ->where('status', 'aktif')
                ->orderBy('nama_layanan')
                ->get(),
            'hariLiburAktif' => HariLibur::query()->sedangBerlangsung()->orderBy('tanggal_mulai')->first(),
        ]);
    }

    public function store(Request $request, LaundryOrderService $service): RedirectResponse
    {
        $data = $request->validate([
            'layanan_laundry_id' => ['required', 'exists:layanan_laundries,id'],
            'berat' => ['nullable', 'numeric', 'min:0.1'],
            'jumlah_item' => ['nullable', 'integer', 'min:1', 'max:10'],
            'metode_penyerahan' => ['required', 'in:antar_sendiri,jemput'],
            'alamat_penjemputan' => ['nullable', 'required_if:metode_penyerahan,jemput', 'string', 'max:1000'],
            'catatan_pelanggan' => ['nullable', 'string', 'max:1000'],
        ]);

        $layanan = LayananLaundry::findOrFail($data['layanan_laundry_id']);

        if ($layanan->tipe_layanan === 'kiloan' && empty($data['berat'])) {
            return back()->withInput()->withErrors(['berat' => 'Berat estimasi wajib diisi untuk layanan kiloan.']);
        }

        if ($layanan->tipe_layanan === 'satuan' && empty($data['jumlah_item'])) {
            return back()->withInput()->withErrors(['jumlah_item' => 'Jumlah item wajib diisi untuk layanan satuan.']);
        }

        $pesanan = $service->createCustomerOrder($request->user(), $data);

        return redirect()
            ->route('customer.orders.show', $pesanan)
            ->with('success', 'Pesanan berhasil dibuat. Silakan bayar melalui QRIS dan upload bukti pembayaran agar admin dapat mengonfirmasi pesanan.');
    }
}

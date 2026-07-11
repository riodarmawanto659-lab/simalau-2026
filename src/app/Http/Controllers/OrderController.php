<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\HariLibur;
use App\Models\LayananLaundry;
use App\Services\LaundryOrderService;
use Illuminate\Http\RedirectResponse;

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

    public function store(StoreOrderRequest $request, LaundryOrderService $service): RedirectResponse
    {
        $pesanan = $service->createCustomerOrder($request->user(), $request->validated());

        return redirect()
            ->route('customer.orders.show', $pesanan)
            ->with('success', 'Pesanan berhasil dibuat. Silakan bayar melalui QRIS dan upload bukti pembayaran agar admin dapat mengonfirmasi pesanan.');
    }
}

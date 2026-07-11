<?php

namespace App\Services;

use App\Models\Pembayaran;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransPaymentService
{
    public function createSnapToken(Pembayaran $pembayaran): Pembayaran
    {
        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum diatur di file .env.');
        }

        $pembayaran->loadMissing('pesanan.pelanggan', 'pesanan.detailPesanans');

        $pesanan = $pembayaran->pesanan;
        $pelanggan = $pesanan?->pelanggan;

        if (! $pesanan) {
            throw new RuntimeException('Data pesanan untuk pembayaran ini tidak ditemukan.');
        }

        $midtransOrderId = $pembayaran->midtrans_order_id
            ?: 'SIMALAU-' . $pembayaran->id . '-' . now()->format('YmdHis');

        $grossAmount = (int) round((float) $pembayaran->total_tagihan);

        if ($grossAmount <= 0) {
            throw new RuntimeException('Total tagihan tidak valid untuk pembayaran Midtrans.');
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => Str::limit((string) ($pelanggan?->nama_lengkap ?: 'Pelanggan Laundry'), 45, ''),
                'email' => $pelanggan?->email,
                'phone' => $pelanggan?->nomor_whatsapp,
            ],
            'item_details' => [
                [
                    'id' => (string) $pesanan->nomor_pesanan,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => Str::limit('Pembayaran Laundry ' . $pesanan->nomor_pesanan, 50, ''),
                ],
            ],
            'callbacks' => [
                'finish' => route('customer.orders.show', $pesanan),
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($this->snapApiUrl(), $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal membuat transaksi Midtrans: ' . $response->body());
        }

        $body = $response->json();

        if (empty($body['token'])) {
            throw new RuntimeException('Response Midtrans tidak memiliki snap token.');
        }

        $pembayaran->forceFill([
            'midtrans_order_id' => $midtransOrderId,
            'snap_token' => $body['token'],
            'snap_redirect_url' => $body['redirect_url'] ?? null,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'belum_dibayar',
            'midtrans_response' => $body,
        ])->save();

        return $pembayaran->refresh();
    }

    public function snapApiUrl(): string
    {
        return config('midtrans.is_production')
            ? config('midtrans.production_snap_api_url')
            : config('midtrans.sandbox_snap_api_url');
    }

    public function snapJsUrl(): string
    {
        return config('midtrans.is_production')
            ? config('midtrans.production_snap_js_url')
            : config('midtrans.sandbox_snap_js_url');
    }
}

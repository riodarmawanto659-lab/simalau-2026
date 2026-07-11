<?php

namespace App\Services;

use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransPaymentService
{
    public function createSnapTransaction(Pembayaran $pembayaran): Pembayaran
    {
        $serverKey = config('midtrans.server_key');

        if (! is_string($serverKey) || trim($serverKey) === '') {
            throw new RuntimeException(
                'MIDTRANS_SERVER_KEY belum terbaca. Pastikan MIDTRANS_SERVER_KEY ada di .env dan config/midtrans.php sudah benar.'
            );
        }

        $snapUrl = $this->getSnapApiUrl();

        $pembayaran->loadMissing('pesanan.pelanggan');

        $pesanan = $pembayaran->pesanan;
        $pelanggan = $pesanan?->pelanggan;

        if (! $pesanan) {
            throw new RuntimeException('Data pesanan tidak ditemukan untuk pembayaran ini.');
        }

        $midtransOrderId = 'SIMALAU-' . $pembayaran->id . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

        $grossAmount = max((int) round((float) $pembayaran->total_tagihan), 1);

        $payload = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $pelanggan?->nama_lengkap ?: 'Pelanggan Laundry',
                'email' => $pelanggan?->email ?: 'customer@simalau.test',
                'phone' => $pelanggan?->nomor_whatsapp ?: '08123456789',
            ],
            'item_details' => [
                [
                    'id' => (string) ($pesanan->nomor_pesanan ?: $pesanan->id),
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Laundry ' . ($pesanan->nomor_pesanan ?: $pesanan->id),
                ],
            ],
            'callbacks' => [
                'finish' => route('customer.payments.midtrans.callback', $pembayaran),
                'error' => route('customer.payments.midtrans.callback', $pembayaran),
                'pending' => route('customer.payments.midtrans.callback', $pembayaran),
            ],
        ];

        $response = Http::withBasicAuth(trim($serverKey), '')
            ->acceptJson()
            ->asJson()
            ->post($snapUrl, $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal membuat transaksi Midtrans: ' . $response->body());
        }

        $body = $response->json();

        if (empty($body['token']) || empty($body['redirect_url'])) {
            throw new RuntimeException('Response Midtrans tidak valid: ' . json_encode($body));
        }

        $pembayaran->forceFill([
            'midtrans_order_id' => $midtransOrderId,
            'snap_token' => $body['token'],
            'snap_redirect_url' => $body['redirect_url'],
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'belum_dibayar',
            'midtrans_response' => $body,
        ])->save();

        return $pembayaran->refresh();
    }

    public function checkTransactionStatus(string $orderId): ?array
    {
        $serverKey = config('midtrans.server_key');
        $environments = [
            'https://api.sandbox.midtrans.com/v2',
            'https://api.midtrans.com/v2',
        ];

        foreach ($environments as $baseUrl) {
            $response = Http::withBasicAuth(trim($serverKey), '')
                ->acceptJson()
                ->get($baseUrl . '/' . $orderId . '/status');

            if (! $response->successful()) {
                continue;
            }

            $json = $response->json();

            if (($json['status_code'] ?? '') === '404') {
                continue;
            }

            return $json;
        }

        return null;
    }

    public function markAsLunasIfSettled(Pembayaran $pembayaran): bool
    {
        if ($pembayaran->status_pembayaran === 'lunas') {
            return false;
        }

        $orderId = $pembayaran->midtrans_order_id;
        if (! $orderId) {
            return false;
        }

        $status = $this->checkTransactionStatus($orderId);
        if (! $status) {
            return false;
        }

        $transactionStatus = $status['transaction_status'] ?? '';
        $fraudStatus = $status['fraud_status'] ?? '';
        $paymentType = $status['payment_type'] ?? null;

        $isSuccess = $transactionStatus === 'settlement'
            || ($transactionStatus === 'capture' && in_array($fraudStatus, ['accept', null, ''], true));

        if (! $isSuccess) {
            return false;
        }

        DB::transaction(function () use ($pembayaran, $status, $transactionStatus, $fraudStatus, $paymentType): void {
            $pembayaran->forceFill([
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'fraud_status' => $fraudStatus,
                'midtrans_response' => $status,
                'status_pembayaran' => 'lunas',
                'nominal_dibayar' => $pembayaran->total_tagihan,
                'kembalian' => 0,
                'tanggal_pembayaran' => now(),
            ])->save();

            $pembayaran->pesanan?->forceFill([
                'status_pembayaran' => 'lunas',
            ])->saveQuietly();
        });

        return true;
    }

    public function snapJsUrl(): string
    {
        $url = config('midtrans.snap_js_url');

        if (! is_string($url) || trim($url) === '') {
            $isProduction = filter_var(config('midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);

            $url = $isProduction
                ? config('midtrans.production_snap_js_url')
                : config('midtrans.sandbox_snap_js_url');
        }

        if (! is_string($url) || trim($url) === '') {
            throw new RuntimeException(
                'MIDTRANS Snap JS URL belum terbaca. Pastikan config/midtrans.php memiliki snap_js_url.'
            );
        }

        return trim($url);
    }

    private function getSnapApiUrl(): string
    {
        $url = config('midtrans.snap_api_url');

        if (! is_string($url) || trim($url) === '') {
            $isProduction = filter_var(config('midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);

            $url = $isProduction
                ? config('midtrans.production_snap_api_url')
                : config('midtrans.sandbox_snap_api_url');
        }

        if (! is_string($url) || trim($url) === '') {
            throw new RuntimeException(
                'MIDTRANS Snap API URL belum terbaca. Pastikan config/midtrans.php memiliki snap_api_url.'
            );
        }

        return trim($url);
    }
}

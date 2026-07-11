<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MidtransPaymentController extends Controller
{
    public function pay(Request $request, Pembayaran $pembayaran, MidtransPaymentService $service): View|RedirectResponse
    {
        $pembayaran->loadMissing('pesanan.pelanggan');

        abort_unless(
            $request->user()?->pelanggan
            && $pembayaran->pesanan?->pelanggan_id === $request->user()->pelanggan->id,
            403
        );

        if ($pembayaran->status_pembayaran === 'lunas') {
            return redirect()
                ->route('customer.orders.show', $pembayaran->pesanan)
                ->with('success', 'Pembayaran pesanan ini sudah lunas.');
        }

        if (! $pembayaran->snap_token) {
            $pembayaran = $service->createSnapToken($pembayaran);
        }

        return view('customer.payments.midtrans', [
            'pembayaran' => $pembayaran->loadMissing('pesanan'),
            'clientKey' => config('midtrans.client_key'),
            'snapJsUrl' => $service->snapJsUrl(),
        ]);
    }

    public function notification(Request $request): JsonResponse
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signatureKey) {
            return response()->json(['message' => 'Invalid notification payload'], 400);
        }

        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));

        if (! hash_equals($validSignature, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pembayaran = Pembayaran::query()
            ->where('midtrans_order_id', $orderId)
            ->first();

        if (! $pembayaran) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        DB::transaction(function () use ($pembayaran, $payload, $transactionStatus, $fraudStatus, $paymentType): void {
            $isSuccess = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && in_array($fraudStatus, ['accept', null], true));

            $isFailed = in_array($transactionStatus, ['cancel', 'deny', 'expire', 'failure'], true);

            $pembayaran->forceFill([
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'fraud_status' => $fraudStatus,
                'midtrans_response' => $payload,
                'status_pembayaran' => $isSuccess ? 'lunas' : 'belum_dibayar',
                'nominal_dibayar' => $isSuccess ? $pembayaran->total_tagihan : 0,
                'kembalian' => 0,
                'tanggal_pembayaran' => $isSuccess ? now() : null,
                'catatan' => $isSuccess
                    ? 'Pembayaran berhasil dikonfirmasi otomatis oleh Midtrans.'
                    : ($isFailed ? 'Pembayaran Midtrans gagal/kedaluwarsa.' : 'Pembayaran Midtrans masih menunggu penyelesaian.'),
            ])->save();
        });

        return response()->json(['message' => 'Notification processed']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\MidtransPaymentService;
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
            $pembayaran->pesanan
            && $pembayaran->pesanan->pelanggan_id === $request->user()->pelanggan?->id,
            403
        );

        if ($pembayaran->status_pembayaran === 'lunas') {
            return redirect()
                ->route('customer.orders.show', $pembayaran->pesanan)
                ->with('success', 'Pembayaran pesanan ini sudah lunas.');
        }

        if ($service->markAsLunasIfSettled($pembayaran)) {
            return redirect()
                ->route('customer.orders.show', $pembayaran->pesanan)
                ->with('success', 'Pembayaran sudah terverifikasi lunas.');
        }

        if (! $pembayaran->snap_token) {
            $pembayaran = $service->createSnapTransaction($pembayaran);
        }

        if (! $pembayaran->snap_token) {
            return redirect()
                ->route('customer.orders.show', $pembayaran->pesanan)
                ->withErrors(['pembayaran' => 'Token pembayaran Midtrans gagal dibuat.']);
        }

        return view('customer.payments.midtrans', [
            'pembayaran' => $pembayaran->loadMissing('pesanan'),
            'clientKey' => config('midtrans.client_key'),
            'snapJsUrl' => $service->snapJsUrl(),
        ]);
    }

    public function callback(Request $request, Pembayaran $pembayaran, MidtransPaymentService $service): RedirectResponse
    {
        if ($pembayaran->status_pembayaran === 'lunas') {
            return redirect()
                ->route('customer.orders.show', $pembayaran->pesanan)
                ->with('success', 'Pembayaran sudah lunas.');
        }

        $transactionStatus = $request->input('transaction_status');

        if (in_array($transactionStatus, ['settlement', 'capture'], true)) {
            $paymentType = $request->input('payment_type');
            $fraudStatus = $request->input('fraud_status');

            DB::transaction(function () use ($pembayaran, $transactionStatus, $fraudStatus, $paymentType, $request): void {
                $pembayaran->forceFill([
                    'transaction_status' => $transactionStatus,
                    'payment_type' => $paymentType,
                    'fraud_status' => $fraudStatus,
                    'midtrans_response' => $request->all(),
                    'status_pembayaran' => 'lunas',
                    'nominal_dibayar' => $pembayaran->total_tagihan,
                    'kembalian' => 0,
                    'tanggal_pembayaran' => now(),
                ])->save();

                $pembayaran->pesanan?->forceFill([
                    'status_pembayaran' => 'lunas',
                ])->saveQuietly();
            });

            return redirect()
                ->route('customer.orders.show', $pembayaran->pesanan)
                ->with('success', 'Pembayaran berhasil!');
        }

        return redirect()
            ->route('customer.orders.show', $pembayaran->pesanan)
            ->with('info', 'Pembayaran sedang diproses.');
    }

    public function notification(Request $request)
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signatureKey) {
            return response()->json(['message' => 'Invalid notification payload'], 400);
        }

        $validSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . config('midtrans.server_key')
        );

        if (! hash_equals($validSignature, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();

        if (! $pembayaran) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        DB::transaction(function () use ($pembayaran, $payload, $transactionStatus, $fraudStatus, $paymentType): void {
            $isSuccess = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && in_array($fraudStatus, ['accept', null], true));

            $pembayaran->forceFill([
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'fraud_status' => $fraudStatus,
                'midtrans_response' => $payload,
                'status_pembayaran' => $isSuccess ? 'lunas' : 'belum_dibayar',
                'nominal_dibayar' => $isSuccess ? $pembayaran->total_tagihan : $pembayaran->nominal_dibayar,
                'kembalian' => 0,
                'tanggal_pembayaran' => $isSuccess ? now() : $pembayaran->tanggal_pembayaran,
            ])->save();

            if ($isSuccess) {
                $pembayaran->pesanan?->forceFill([
                    'status_pembayaran' => 'lunas',
                ])->saveQuietly();
            }
        });

        return response()->json(['message' => 'Notification processed']);
    }
}
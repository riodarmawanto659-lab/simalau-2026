<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PengaturanSistem;
use App\Models\Pesanan;
use App\Services\MidtransPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $pelanggan = $request->user()->pelanggan;
        abort_unless($pelanggan, 403);

        $orders = $pelanggan->pesanans()->latest('tanggal_masuk');

        return view('customer.dashboard', [
            'pelanggan' => $pelanggan,
            'pesananTerbaru' => (clone $orders)->take(5)->get(),
            'totalPesanan' => (clone $orders)->count(),
            'pesananAktif' => (clone $orders)->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])->count(),
            'siapDiambil' => (clone $orders)->where('status_pesanan', 'siap_diambil')->count(),
            'belumDibayar' => (clone $orders)->whereIn('status_pembayaran', ['belum_dibayar', 'menunggu_konfirmasi'])->count(),
        ]);
    }

    public function orders(Request $request)
    {
        $pelanggan = $request->user()->pelanggan;
        abort_unless($pelanggan, 403);

        $status = $request->query('status');
        $query = $pelanggan->pesanans()
            ->with('detailPesanans.layananLaundry')
            ->latest('tanggal_masuk');

        if ($status && array_key_exists($status, Pesanan::STATUS_LABELS)) {
            $query->where('status_pesanan', $status);
        }

        return view('customer.orders.index', [
            'pesanans' => $query->paginate(10)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function orderDetail(Request $request, Pesanan $pesanan, MidtransPaymentService $midtrans)
    {
        abort_unless($pesanan->pelanggan_id === $request->user()->pelanggan?->id, 403);

        if ($pesanan->status_pembayaran !== 'lunas' && $pesanan->pembayaran) {
            if ($midtrans->markAsLunasIfSettled($pesanan->pembayaran)) {
                $pesanan->refresh();
            }
        }

        $pesanan->load(['detailPesanans.layananLaundry', 'pembayaran', 'riwayatStatuses' => fn ($query) => $query->orderBy('tanggal_perubahan')]);

        return view('customer.orders.show', [
            'pesanan' => $pesanan,
            'pengaturanSistem' => PengaturanSistem::query()->latest()->first(),
        ]);
    }

    public function payOrder(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $pelanggan = $request->user()->pelanggan;
        abort_unless($pelanggan && $pesanan->pelanggan_id === $pelanggan->id, 403);

        if ($pesanan->status_pesanan === 'dibatalkan') {
            return back()->withErrors(['bukti_pembayaran' => 'Pesanan yang dibatalkan tidak bisa dibayar.']);
        }

        if ((float) $pesanan->total_biaya <= 0) {
            return back()->withErrors(['bukti_pembayaran' => 'Pembayaran belum bisa diproses karena total tagihan belum tersedia.']);
        }

        if ($pesanan->status_pembayaran === 'lunas') {
            return back()->with('success', 'Pesanan ini sudah lunas.');
        }

        $data = $request->validate([
            'bukti_pembayaran' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $buktiPembayaran = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');

        try {
            DB::transaction(function () use ($pesanan, $data, $buktiPembayaran): void {
                $catatan = 'Bukti pembayaran QRIS dikirim oleh pelanggan dan menunggu verifikasi admin.';

                if (! empty($data['catatan'])) {
                    $catatan .= "\nCatatan pelanggan: " . $data['catatan'];
                }

                $pembayaranLama = $pesanan->pembayaran;

                Pembayaran::updateOrCreate(
                    ['pesanan_id' => $pesanan->id],
                    [
                        'metode_pembayaran' => 'qris',
                        'total_tagihan' => $pesanan->total_biaya,
                        'nominal_dibayar' => 0,
                        'status_pembayaran' => 'menunggu_konfirmasi',
                        'tanggal_pembayaran' => null,
                        'bukti_pembayaran' => $buktiPembayaran,
                        'catatan' => $catatan,
                    ]
                );

                if ($pembayaranLama?->bukti_pembayaran && $pembayaranLama->bukti_pembayaran !== $buktiPembayaran) {
                    Storage::disk('public')->delete($pembayaranLama->bukti_pembayaran);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($buktiPembayaran);

            throw $exception;
        }

        return redirect()
            ->route('customer.orders.show', $pesanan)
            ->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan mengecek dan mengonfirmasi pesanan Anda.');
    }

    public function profile(Request $request)
    {
        abort_unless($request->user()->pelanggan, 403);

        return view('customer.profile', [
            'pelanggan' => $request->user()->pelanggan,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $pelanggan = $request->user()->pelanggan;
        abort_unless($pelanggan, 403);

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nomor_whatsapp' => ['required', 'regex:/^[0-9+ ]+$/', 'max:20'],
            'alamat' => ['required', 'string', 'max:1000'],
        ]);

        $pelanggan->update($data);
        $request->user()->update(['name' => $data['nama_lengkap']]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}

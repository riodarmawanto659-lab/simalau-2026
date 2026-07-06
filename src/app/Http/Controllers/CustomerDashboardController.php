<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            'belumDibayar' => (clone $orders)->where('status_pembayaran', 'belum_dibayar')->count(),
        ]);
    }

    public function orders(Request $request)
    {
        $pelanggan = $request->user()->pelanggan;
        abort_unless($pelanggan, 403);

        $status = $request->query('status');
        $query = $pelanggan->pesanans()
            ->with('detailPesanans')
            ->latest('tanggal_masuk');

        if ($status && array_key_exists($status, Pesanan::STATUS_LABELS)) {
            $query->where('status_pesanan', $status);
        }

        return view('customer.orders.index', [
            'pesanans' => $query->paginate(10)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function orderDetail(Request $request, Pesanan $pesanan)
    {
        abort_unless($pesanan->pelanggan_id === $request->user()->pelanggan?->id, 403);

        return view('customer.orders.show', [
            'pesanan' => $pesanan->load(['detailPesanans', 'pembayaran', 'riwayatStatuses' => fn ($query) => $query->orderBy('tanggal_perubahan')]),
        ]);
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

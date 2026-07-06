<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use App\Models\LayananLaundry;
use App\Models\Pesanan;
use App\Models\PengaturanSistem;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'layananUnggulan' => LayananLaundry::with('kategoriLayanan')
                ->where('status', 'aktif')
                ->orderBy('nama_layanan')
                ->take(5)
                ->get(),
            'pengaturan' => PengaturanSistem::query()->first(),
        ]);
    }

    public function services()
    {
        return view('public.services', [
            'kategoriLayanans' => KategoriLayanan::query()
                ->where('status', 'aktif')
                ->with(['layananLaundries' => fn ($query) => $query->where('status', 'aktif')->orderBy('tarif')])
                ->orderBy('urutan')
                ->get(),
        ]);
    }

    public function serviceDetail(LayananLaundry $layananLaundry)
    {
        abort_unless($layananLaundry->status === 'aktif', 404);

        return view('public.service-detail', [
            'layanan' => $layananLaundry->load('kategoriLayanan'),
        ]);
    }

    public function status()
    {
        return view('public.status');
    }

    public function checkStatus(Request $request)
    {
        $data = $request->validate([
            'nomor_pesanan' => ['required', 'string', 'max:255'],
        ]);

        $pesanan = Pesanan::with(['pelanggan', 'detailPesanans', 'riwayatStatuses' => fn ($query) => $query->orderBy('tanggal_perubahan')])
            ->where('nomor_pesanan', $data['nomor_pesanan'])
            ->first();

        if (! $pesanan) {
            return back()
                ->withInput()
                ->withErrors(['nomor_pesanan' => 'Nomor pesanan tidak ditemukan.']);
        }

        return view('public.status-result', compact('pesanan'));
    }
}

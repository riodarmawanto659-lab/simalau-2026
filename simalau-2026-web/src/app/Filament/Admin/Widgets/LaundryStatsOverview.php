<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Pembayaran;
use App\Models\PengingatPengambilan;
use App\Models\Pesanan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaundryStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pesanan Hari Ini', Pesanan::whereDate('tanggal_masuk', today())->count())
                ->description('Transaksi masuk hari ini')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Antrean Aktif', Pesanan::whereNotIn('status_pesanan', ['menunggu_konfirmasi', 'selesai', 'dibatalkan'])->count())
                ->description('Diproses dengan FIFO')
                ->icon('heroicon-o-list-bullet')
                ->color('warning'),

            Stat::make('Siap Diambil', Pesanan::where('status_pesanan', 'siap_diambil')->count())
                ->description('Menunggu pelanggan')
                ->icon('heroicon-o-archive-box')
                ->color('success'),

            Stat::make('Reminder Aktif', PengingatPengambilan::where('status_pengingat', 'aktif')->count())
                ->description('Belum diambil >= 3 hari')
                ->icon('heroicon-o-bell-alert')
                ->color('danger'),

            Stat::make('Omzet Lunas', 'Rp ' . number_format((float) Pembayaran::where('status_pembayaran', 'lunas')->sum('total_tagihan'), 0, ',', '.'))
                ->description('Total pembayaran berhasil')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}

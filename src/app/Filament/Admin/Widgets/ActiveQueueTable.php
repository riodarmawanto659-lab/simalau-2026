<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Pesanan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ActiveQueueTable extends BaseWidget
{
    protected static ?string $heading = 'Antrean Pengerjaan FIFO';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('urutan_antrian')
                    ->label('Antrean')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_pesanan')
                    ->label('No. Pesanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pelanggan.nama_lengkap')
                    ->label('Pelanggan')
                    ->description(fn (Pesanan $record): ?string => $record->pelanggan?->nomor_whatsapp),
                Tables\Columns\TextColumn::make('status_pesanan')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Pesanan::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu_proses' => 'warning',
                        'sedang_dicuci', 'sedang_dikeringkan', 'sedang_disetrika' => 'info',
                        'siap_diambil' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y, H:i'),
                Tables\Columns\TextColumn::make('estimasi_selesai')
                    ->label('Estimasi')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-'),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return Pesanan::query()
            ->with('pelanggan')
            ->whereNotIn('status_pesanan', ['menunggu_konfirmasi', 'selesai', 'dibatalkan'])
            ->orderByRaw('COALESCE(urutan_antrian, 999999) asc')
            ->orderBy('tanggal_masuk');
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PembayaranResource\Pages;
use App\Models\Pembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->description('Verifikasi pembayaran QRIS yang dikirim pelanggan.')
                    ->schema([
                        Forms\Components\Select::make('pesanan_id')
                            ->label('Nomor Pesanan')
                            ->relationship('pesanan', 'nomor_pesanan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                $pesanan = \App\Models\Pesanan::find($state);

                                if (! $pesanan) {
                                    return;
                                }

                                $set('total_tagihan', $pesanan->total_biaya);
                            }),

                        Forms\Components\TextInput::make('nomor_pembayaran')
                            ->label('Nomor Pembayaran')
                            ->placeholder('Contoh: PAY-20260705-0001')
                            ->default(fn (): string => Pembayaran::generateNomorPembayaran())
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->dehydrated(true),

                        Forms\Components\Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'qris' => 'QRIS',
                            ])
                            ->default('qris')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->helperText('Gunakan tombol Konfirmasi Pembayaran setelah bukti QRIS valid.')
                            ->options([
                                'belum_dibayar' => 'Belum Dibayar',
                                'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                                'lunas' => 'Lunas',
                            ])
                            ->default('belum_dibayar')
                            ->required()
                            ->native(false)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Nominal Pembayaran')
                    ->description('Nominal dibayar akan diisi penuh saat admin mengonfirmasi pembayaran QRIS.')
                    ->schema([
                        Forms\Components\TextInput::make('total_tagihan')
                            ->label('Total Tagihan')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('nominal_dibayar')
                            ->label('Nominal Dibayar')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set): void {
                                $totalTagihan = (float) ($get('total_tagihan') ?? 0);
                                $nominalDibayar = (float) ($state ?? 0);

                                $set('kembalian', max($nominalDibayar - $totalTagihan, 0));
                            }),

                        Forms\Components\TextInput::make('kembalian')
                            ->label('Kembalian')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->readOnly(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Tanggal dan Catatan')
                    ->schema([
                        Forms\Components\DateTimePicker::make('tanggal_pembayaran')
                            ->label('Tanggal Pembayaran')
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\FileUpload::make('bukti_pembayaran')
                            ->label('Bukti Pembayaran QRIS')
                            ->disk('public')
                            ->directory('bukti-pembayaran')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(4096)
                            ->openable()
                            ->downloadable()
                            ->helperText('Bukti dari pelanggan. Format JPG, PNG, WEBP, atau PDF. Maksimal 4 MB.'),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Pembayaran')
                            ->placeholder('Contoh: Bukti QRIS valid dan sudah dikonfirmasi.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pembayaran')
                    ->label('Nomor Pembayaran')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor pembayaran berhasil disalin'),

                Tables\Columns\TextColumn::make('pesanan.nomor_pesanan')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor pesanan berhasil disalin'),

                Tables\Columns\TextColumn::make('pesanan.pelanggan.nama_lengkap')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'qris' => 'QRIS',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'qris' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nominal_dibayar')
                    ->label('Dibayar')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('kembalian')
                    ->label('Kembalian')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_dibayar' => 'Belum Dibayar',
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'lunas' => 'Lunas',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'belum_dibayar' => 'warning',
                        'menunggu_konfirmasi' => 'info',
                        'lunas' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('bukti_pembayaran')
                    ->label('Bukti')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Lihat Bukti' : 'Belum Ada')
                    ->url(fn (Pembayaran $record): ?string => $record->bukti_pembayaran_url)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color(fn (?string $state): string => filled($state) ? 'info' : 'gray'),

                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum dibayar')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('metode_pembayaran')
                    ->label('Filter Metode Pembayaran')
                    ->options([
                        'qris' => 'QRIS',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->label('Filter Status Pembayaran')
                    ->options([
                        'belum_dibayar' => 'Belum Dibayar',
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'lunas' => 'Lunas',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat_bukti')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->url(fn (Pembayaran $record): ?string => $record->bukti_pembayaran_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Pembayaran $record): bool => filled($record->bukti_pembayaran_url)),

                Tables\Actions\Action::make('konfirmasi_pembayaran')
                    ->label('Konfirmasi Pembayaran')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Pembayaran $record): bool => $record->status_pembayaran === 'menunggu_konfirmasi' && filled($record->bukti_pembayaran))
                    ->action(function (Pembayaran $record): void {
                        $record->update([
                            'nominal_dibayar' => $record->total_tagihan,
                            'status_pembayaran' => 'lunas',
                            'tanggal_pembayaran' => now(),
                            'catatan' => trim(($record->catatan ? $record->catatan . PHP_EOL : '') . 'Pembayaran QRIS dikonfirmasi admin.'),
                        ]);

                        Notification::make()
                            ->title('Pembayaran QRIS dikonfirmasi')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('minta_upload_ulang')
                    ->label('Minta Upload Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Pembayaran $record): bool => $record->status_pembayaran === 'menunggu_konfirmasi')
                    ->action(function (Pembayaran $record): void {
                        if ($record->bukti_pembayaran) {
                            Storage::disk('public')->delete($record->bukti_pembayaran);
                        }

                        $record->update([
                            'nominal_dibayar' => 0,
                            'status_pembayaran' => 'belum_dibayar',
                            'tanggal_pembayaran' => null,
                            'bukti_pembayaran' => null,
                            'catatan' => trim(($record->catatan ? $record->catatan . PHP_EOL : '') . 'Admin meminta pelanggan upload ulang bukti pembayaran.'),
                        ]);

                        Notification::make()
                            ->title('Status pembayaran dikembalikan')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Kelola'),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada data pembayaran')
            ->emptyStateDescription('Data pembayaran transaksi laundry akan muncul di halaman ini.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}

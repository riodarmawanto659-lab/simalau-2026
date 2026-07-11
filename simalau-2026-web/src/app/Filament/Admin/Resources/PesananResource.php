<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PesananResource\Pages;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Operasional Laundry';

    protected static ?string $navigationLabel = 'Pesanan Laundry';

    protected static ?string $modelLabel = 'Pesanan Laundry';

    protected static ?string $pluralModelLabel = 'Pesanan Laundry';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pesanan')
                    ->description('Data utama pesanan laundry pelanggan.')
                    ->schema([
                        Forms\Components\Select::make('pelanggan_id')
                            ->label('Pelanggan')
                            ->relationship('pelanggan', 'nama_lengkap')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('nomor_pesanan')
                            ->label('Nomor Pesanan')
                            ->placeholder('Contoh: LDR-20260705-0001')
                            ->default(fn (): string => Pesanan::generateNomorPesanan())
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->dehydrated(true),

                        Forms\Components\Select::make('metode_penyerahan')
                            ->label('Metode Penyerahan')
                            ->options([
                                'antar_sendiri' => 'Antar Sendiri ke Outlet',
                                'jemput' => 'Minta Dijemput',
                            ])
                            ->default('antar_sendiri')
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('urutan_antrian')
                            ->label('Urutan Antrean FIFO')
                            ->helperText('Urutan pengerjaan berdasarkan waktu masuk. Angka lebih kecil diproses lebih dulu.')
                            ->numeric()
                            ->readOnly()
                            ->default(null),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tanggal dan Estimasi')
                    ->description('Tanggal ini digunakan untuk antrean, estimasi selesai, dan reminder pengambilan.')
                    ->schema([
                        Forms\Components\DateTimePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\DateTimePicker::make('estimasi_selesai')
                            ->label('Estimasi Selesai')
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\DateTimePicker::make('tanggal_siap_diambil')
                            ->label('Tanggal Siap Diambil')
                            ->helperText('Diisi saat status berubah menjadi Siap Diambil.')
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\DateTimePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai / Sudah Diambil')
                            ->seconds(false)
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status Pesanan')
                    ->description('Status cucian harus mengikuti urutan proses laundry.')
                    ->schema([
                        Forms\Components\Select::make('status_pesanan')
                            ->label('Status Cucian')
                            ->helperText('Pesanan baru hanya bisa dikonfirmasi setelah pembayaran QRIS sudah lunas.')
                            ->options([
                                'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                                'menunggu_proses' => 'Menunggu Proses',
                                'sedang_dicuci' => 'Sedang Dicuci',
                                'sedang_dikeringkan' => 'Sedang Dikeringkan',
                                'sedang_disetrika' => 'Sedang Disetrika',
                                'siap_diambil' => 'Siap Diambil',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('menunggu_konfirmasi')
                            ->required()
                            ->native(false)
                            ->disabled(fn (?Pesanan $record): bool => filled($record))
                            ->dehydrated(fn (?Pesanan $record): bool => blank($record)),

                        Forms\Components\Select::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->helperText('Status pembayaran dikelola dari menu Pembayaran setelah bukti QRIS diverifikasi.')
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

                Forms\Components\Section::make('Biaya Pesanan')
                    ->description('Total biaya dihitung dari detail layanan pesanan.')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('diskon')
                            ->label('Diskon')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('total_biaya')
                            ->label('Total Biaya')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Catatan dan Penjemputan')
                    ->schema([
                        Forms\Components\Textarea::make('alamat_penjemputan')
                            ->label('Alamat Penjemputan')
                            ->placeholder('Diisi jika pelanggan memilih metode penjemputan.')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('catatan_pelanggan')
                            ->label('Catatan Pelanggan')
                            ->placeholder('Catatan dari pelanggan terkait cucian.')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Catatan Admin')
                            ->placeholder('Catatan internal admin.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_masuk', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pesanan')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor pesanan berhasil disalin'),

                Tables\Columns\TextColumn::make('pelanggan.nama_lengkap')
                    ->label('Pelanggan')
                    ->description(fn (Pesanan $record): ?string => $record->pelanggan?->nomor_whatsapp)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimasi_selesai')
                    ->label('Estimasi Selesai')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_pesanan')
                    ->label('Status Cucian')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'menunggu_proses' => 'Menunggu Proses',
                        'sedang_dicuci' => 'Sedang Dicuci',
                        'sedang_dikeringkan' => 'Sedang Dikeringkan',
                        'sedang_disetrika' => 'Sedang Disetrika',
                        'siap_diambil' => 'Siap Diambil',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu_konfirmasi' => 'gray',
                        'menunggu_proses' => 'warning',
                        'sedang_dicuci' => 'info',
                        'sedang_dikeringkan' => 'info',
                        'sedang_disetrika' => 'info',
                        'siap_diambil' => 'success',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_pembayaran')
                    ->label('Pembayaran')
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

                Tables\Columns\TextColumn::make('total_biaya')
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('urutan_antrian')
                    ->label('Antrean')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('metode_penyerahan')
                    ->label('Penyerahan')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'antar_sendiri' => 'Antar Sendiri',
                        'jemput' => 'Dijemput',
                        default => '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pesanan')
                    ->label('Filter Status Cucian')
                    ->options([
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'menunggu_proses' => 'Menunggu Proses',
                        'sedang_dicuci' => 'Sedang Dicuci',
                        'sedang_dikeringkan' => 'Sedang Dikeringkan',
                        'sedang_disetrika' => 'Sedang Disetrika',
                        'siap_diambil' => 'Siap Diambil',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->label('Filter Pembayaran')
                    ->options([
                        'belum_dibayar' => 'Belum Dibayar',
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'lunas' => 'Lunas',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('metode_penyerahan')
                    ->label('Filter Penyerahan')
                    ->options([
                        'antar_sendiri' => 'Antar Sendiri ke Outlet',
                        'jemput' => 'Minta Dijemput',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Kelola'),

                Tables\Actions\Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Pesanan $record): bool => $record->status_pesanan === 'menunggu_konfirmasi' && $record->status_pembayaran === 'lunas')
                    ->action(function (Pesanan $record): void {
                        if (! $record->updateStatus('menunggu_proses', auth()->user(), 'Pesanan dan pembayaran sudah diverifikasi admin.')) {
                            Notification::make()
                                ->title('Pesanan belum bisa dikonfirmasi')
                                ->body('Pastikan pembayaran QRIS sudah dikonfirmasi terlebih dahulu.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Pesanan masuk antrean FIFO')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('status_berikutnya')
                    ->label('Status Berikutnya')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Pesanan $record): bool => $record->status_pesanan !== 'menunggu_konfirmasi' && filled($record->nextStatus()))
                    ->action(function (Pesanan $record): void {
                        $nextStatus = $record->nextStatus();

                        if ($nextStatus) {
                            $record->updateStatus($nextStatus, auth()->user());
                        }

                        Notification::make()
                            ->title('Status cucian diperbarui')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('batalkan')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Pesanan $record): bool => in_array($record->status_pesanan, ['menunggu_konfirmasi', 'menunggu_proses'], true))
                    ->action(function (Pesanan $record): void {
                        $record->forceFill(['status_pesanan' => 'dibatalkan'])->save();

                        Notification::make()
                            ->title('Pesanan dibatalkan')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada pesanan laundry')
            ->emptyStateDescription('Pesanan pelanggan dan transaksi laundry akan muncul di halaman ini.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
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
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}

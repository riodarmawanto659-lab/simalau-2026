<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RiwayatStatusResource\Pages;
use App\Models\RiwayatStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RiwayatStatusResource extends Resource
{
    protected static ?string $model = RiwayatStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Operasional Laundry';

    protected static ?string $navigationLabel = 'Riwayat Status';

    protected static ?string $modelLabel = 'Riwayat Status';

    protected static ?string $pluralModelLabel = 'Riwayat Status';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Riwayat Status')
                    ->description('Catatan perubahan status cucian pada setiap pesanan laundry.')
                    ->schema([
                        Forms\Components\Select::make('pesanan_id')
                            ->label('Nomor Pesanan')
                            ->relationship('pesanan', 'nomor_pesanan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('user_id')
                            ->label('Admin yang Mengubah')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih admin')
                            ->native(false),

                        Forms\Components\Select::make('status_sebelumnya')
                            ->label('Status Sebelumnya')
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
                            ->placeholder('Belum ada status sebelumnya')
                            ->native(false),

                        Forms\Components\Select::make('status_baru')
                            ->label('Status Baru')
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
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('tanggal_perubahan')
                            ->label('Tanggal Perubahan')
                            ->required()
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Perubahan')
                            ->placeholder('Contoh: Cucian mulai masuk tahap pencucian.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_perubahan', 'desc')
            ->columns([
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

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diubah Oleh')
                    ->placeholder('Sistem / Tidak diketahui')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_sebelumnya')
                    ->label('Status Sebelumnya')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'menunggu_proses' => 'Menunggu Proses',
                        'sedang_dicuci' => 'Sedang Dicuci',
                        'sedang_dikeringkan' => 'Sedang Dikeringkan',
                        'sedang_disetrika' => 'Sedang Disetrika',
                        'siap_diambil' => 'Siap Diambil',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        null => '-',
                        default => '-',
                    })
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status_baru')
                    ->label('Status Baru')
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

                Tables\Columns\TextColumn::make('tanggal_perubahan')
                    ->label('Tanggal Perubahan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_baru')
                    ->label('Filter Status Baru')
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
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada riwayat status')
            ->emptyStateDescription('Riwayat perubahan status cucian akan muncul di halaman ini.')
            ->emptyStateIcon('heroicon-o-clock');
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
            'index' => Pages\ListRiwayatStatuses::route('/'),
            'create' => Pages\CreateRiwayatStatus::route('/create'),
            'edit' => Pages\EditRiwayatStatus::route('/{record}/edit'),
        ];
    }
}
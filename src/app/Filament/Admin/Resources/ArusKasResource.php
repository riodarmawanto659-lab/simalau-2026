<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ArusKasResource\Pages;
use App\Models\ArusKas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArusKasResource extends Resource
{
    protected static ?string $model = ArusKas::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Arus Kas';

    protected static ?string $modelLabel = 'Arus Kas';

    protected static ?string $pluralModelLabel = 'Arus Kas';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Arus Kas')
                    ->description('Catat kas masuk dan kas keluar untuk kebutuhan laporan keuangan laundry.')
                    ->schema([
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Arus Kas')
                            ->options([
                                'masuk' => 'Kas Masuk',
                                'keluar' => 'Kas Keluar',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('kategori')
                            ->label('Kategori')
                            ->options([
                                'Pembayaran Laundry' => 'Pembayaran Laundry',
                                'Pembelian Deterjen' => 'Pembelian Deterjen',
                                'Biaya Listrik' => 'Biaya Listrik',
                                'Biaya Air' => 'Biaya Air',
                                'Gaji Karyawan' => 'Gaji Karyawan',
                                'Perawatan Mesin' => 'Perawatan Mesin',
                                'Perlengkapan Laundry' => 'Perlengkapan Laundry',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->searchable()
                            ->native(false),

                        Forms\Components\TextInput::make('judul')
                            ->label('Judul Transaksi')
                            ->placeholder('Contoh: Pembayaran Pesanan LDR-20260705-0001')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nominal')
                            ->label('Nominal')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Relasi Transaksi')
                    ->description('Opsional. Hubungkan arus kas dengan pesanan atau pembayaran jika sumbernya berasal dari transaksi laundry.')
                    ->schema([
                        Forms\Components\Select::make('pesanan_id')
                            ->label('Nomor Pesanan')
                            ->relationship('pesanan', 'nomor_pesanan')
                            ->searchable()
                            ->preload()
                            ->placeholder('Tidak terkait pesanan')
                            ->native(false),

                        Forms\Components\Select::make('pembayaran_id')
                            ->label('Nomor Pembayaran')
                            ->relationship('pembayaran', 'nomor_pembayaran')
                            ->searchable()
                            ->preload()
                            ->placeholder('Tidak terkait pembayaran')
                            ->native(false),

                        Forms\Components\Select::make('user_id')
                            ->label('Dicatat Oleh')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih admin pencatat')
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Keterangan')
                    ->schema([
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tuliskan catatan tambahan jika diperlukan.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'masuk' => 'Kas Masuk',
                        'keluar' => 'Kas Keluar',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'keluar' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Transaksi')
                    ->description(fn (ArusKas $record): ?string => $record->keterangan)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pesanan.nomor_pesanan')
                    ->label('Nomor Pesanan')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pembayaran.nomor_pembayaran')
                    ->label('Nomor Pembayaran')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dicatat Oleh')
                    ->placeholder('Tidak diketahui')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Filter Jenis Arus Kas')
                    ->options([
                        'masuk' => 'Kas Masuk',
                        'keluar' => 'Kas Keluar',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'Pembayaran Laundry' => 'Pembayaran Laundry',
                        'Pembelian Deterjen' => 'Pembelian Deterjen',
                        'Biaya Listrik' => 'Biaya Listrik',
                        'Biaya Air' => 'Biaya Air',
                        'Gaji Karyawan' => 'Gaji Karyawan',
                        'Perawatan Mesin' => 'Perawatan Mesin',
                        'Perlengkapan Laundry' => 'Perlengkapan Laundry',
                        'Lainnya' => 'Lainnya',
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
            ->emptyStateHeading('Belum ada data arus kas')
            ->emptyStateDescription('Catat kas masuk dan kas keluar untuk memantau kondisi keuangan laundry.')
            ->emptyStateIcon('heroicon-o-chart-bar-square');
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
            'index' => Pages\ListArusKas::route('/'),
            'create' => Pages\CreateArusKas::route('/create'),
            'edit' => Pages\EditArusKas::route('/{record}/edit'),
        ];
    }
}
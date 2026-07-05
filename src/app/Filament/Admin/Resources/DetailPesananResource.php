<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DetailPesananResource\Pages;
use App\Models\DetailPesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DetailPesananResource extends Resource
{
    protected static ?string $model = DetailPesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Operasional Laundry';

    protected static ?string $navigationLabel = 'Detail Pesanan';

    protected static ?string $modelLabel = 'Detail Pesanan';

    protected static ?string $pluralModelLabel = 'Detail Pesanan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Detail Pesanan')
                    ->description('Rincian layanan yang digunakan dalam satu pesanan laundry.')
                    ->schema([
                        Forms\Components\Select::make('pesanan_id')
                            ->label('Nomor Pesanan')
                            ->relationship('pesanan', 'nomor_pesanan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('layanan_laundry_id')
                            ->label('Layanan Laundry')
                            ->relationship('layananLaundry', 'nama_layanan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                $layanan = \App\Models\LayananLaundry::find($state);

                                if (! $layanan) {
                                    return;
                                }

                                $set('nama_layanan', $layanan->nama_layanan);
                                $set('tipe_layanan', $layanan->tipe_layanan);
                                $set('satuan_hitung', $layanan->satuan_hitung);
                                $set('harga_satuan', $layanan->tarif);
                            }),

                        Forms\Components\TextInput::make('nama_layanan')
                            ->label('Nama Layanan')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Disimpan sebagai histori agar tidak berubah ketika master layanan diperbarui.'),

                        Forms\Components\Select::make('tipe_layanan')
                            ->label('Tipe Layanan')
                            ->options([
                                'kiloan' => 'Kiloan',
                                'satuan' => 'Satuan',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Jumlah Cucian')
                    ->description('Isi berat untuk layanan kiloan atau jumlah item untuk layanan satuan.')
                    ->schema([
                        Forms\Components\TextInput::make('berat')
                            ->label('Berat Cucian')
                            ->suffix('kg')
                            ->numeric()
                            ->minValue(0)
                            ->default(null)
                            ->visible(fn (Forms\Get $get): bool => $get('tipe_layanan') === 'kiloan'),

                        Forms\Components\TextInput::make('jumlah_item')
                            ->label('Jumlah Item')
                            ->numeric()
                            ->minValue(0)
                            ->default(null)
                            ->visible(fn (Forms\Get $get): bool => $get('tipe_layanan') === 'satuan'),

                        Forms\Components\Select::make('satuan_hitung')
                            ->label('Satuan Hitung')
                            ->options([
                                'kg' => 'Kilogram (kg)',
                                'pcs' => 'Pcs',
                                'pasang' => 'Pasang',
                                'm2' => 'Meter Persegi (m²)',
                            ])
                            ->default('kg')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Perhitungan Biaya')
                    ->description('Harga satuan dan subtotal untuk detail layanan pesanan.')
                    ->schema([
                        Forms\Components\TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Subtotal = berat/jumlah item × harga satuan.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Detail Pesanan')
                            ->placeholder('Contoh: Noda kopi di kemeja, sepatu putih, jangan pakai pewangi pekat.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
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

                Tables\Columns\TextColumn::make('nama_layanan')
                    ->label('Layanan')
                    ->description(fn (DetailPesanan $record): string => match ($record->tipe_layanan) {
                        'kiloan' => 'Layanan Kiloan',
                        'satuan' => 'Layanan Satuan',
                        default => '-',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_display')
                    ->label('Jumlah')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_layanan')
                    ->label('Filter Tipe Layanan')
                    ->options([
                        'kiloan' => 'Kiloan',
                        'satuan' => 'Satuan',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('layanan_laundry_id')
                    ->label('Filter Layanan')
                    ->relationship('layananLaundry', 'nama_layanan')
                    ->searchable()
                    ->preload()
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
            ->emptyStateHeading('Belum ada detail pesanan')
            ->emptyStateDescription('Detail layanan dari pesanan laundry akan muncul di halaman ini.')
            ->emptyStateIcon('heroicon-o-list-bullet');
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
            'index' => Pages\ListDetailPesanans::route('/'),
            'create' => Pages\CreateDetailPesanan::route('/create'),
            'edit' => Pages\EditDetailPesanan::route('/{record}/edit'),
        ];
    }
}
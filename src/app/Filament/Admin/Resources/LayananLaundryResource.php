<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LayananLaundryResource\Pages;
use App\Models\LayananLaundry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LayananLaundryResource extends Resource
{
    protected static ?string $model = LayananLaundry::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Master Data Laundry';

    protected static ?string $navigationLabel = 'Layanan Laundry';

    protected static ?string $modelLabel = 'Layanan Laundry';

    protected static ?string $pluralModelLabel = 'Layanan Laundry';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Layanan')
                    ->description('Kelola layanan laundry yang akan ditampilkan kepada pelanggan.')
                    ->schema([
                        Forms\Components\Select::make('kategori_layanan_id')
                            ->label('Kategori Layanan')
                            ->relationship(
                                name: 'kategoriLayanan',
                                titleAttribute: 'nama_kategori'
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('nama_layanan')
                            ->label('Nama Layanan')
                            ->placeholder('Contoh: Cuci Setrika Reguler')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Slug dibuat otomatis dari nama layanan. Digunakan untuk kebutuhan URL dan sistem.')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('tipe_layanan')
                            ->label('Tipe Layanan')
                            ->options([
                                'kiloan' => 'Kiloan',
                                'satuan' => 'Satuan',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('tarif')
                            ->label('Tarif')
                            ->prefix('Rp')
                            ->placeholder('Contoh: 8000')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('estimasi_hari')
                            ->label('Estimasi Pengerjaan')
                            ->suffix('hari')
                            ->helperText('Lama estimasi pengerjaan berdasarkan jenis layanan.')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1),

                        Forms\Components\TextInput::make('minimal_order')
                            ->label('Minimal Order')
                            ->helperText('Kosongkan jika tidak ada minimal order.')
                            ->numeric()
                            ->minValue(0)
                            ->default(null),

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

                        Forms\Components\Select::make('status')
                            ->label('Status Layanan')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Deskripsi Layanan')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->placeholder('Tuliskan informasi singkat tentang layanan ini.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nama_layanan', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('nama_layanan')
                    ->label('Nama Layanan')
                    ->description(fn (LayananLaundry $record): ?string => $record->deskripsi)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategoriLayanan.nama_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_layanan')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'kiloan' => 'Kiloan',
                        'satuan' => 'Satuan',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'kiloan' => 'info',
                        'satuan' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tarif')
                    ->label('Tarif')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimasi_hari')
                    ->label('Estimasi')
                    ->formatStateUsing(fn ($state): string => $state . ' hari')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('minimal_order')
                    ->label('Minimal Order')
                    ->formatStateUsing(fn ($state): string => $state ? (string) $state : '-')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('satuan_hitung')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_layanan_id')
                    ->label('Filter Kategori')
                    ->relationship('kategoriLayanan', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\SelectFilter::make('tipe_layanan')
                    ->label('Filter Tipe Layanan')
                    ->options([
                        'kiloan' => 'Kiloan',
                        'satuan' => 'Satuan',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
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
            ->emptyStateHeading('Belum ada layanan laundry')
            ->emptyStateDescription('Tambahkan layanan laundry seperti cuci setrika reguler, express, cuci sepatu, cuci selimut, atau cuci karpet.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
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
            'index' => Pages\ListLayananLaundries::route('/'),
            'create' => Pages\CreateLayananLaundry::route('/create'),
            'edit' => Pages\EditLayananLaundry::route('/{record}/edit'),
        ];
    }
}
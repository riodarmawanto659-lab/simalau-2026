<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HariLiburResource\Pages;
use App\Models\HariLibur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Hari Libur';

    protected static ?string $modelLabel = 'Hari Libur';

    protected static ?string $pluralModelLabel = 'Hari Libur';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Hari Libur')
                    ->description('Kelola hari libur nasional atau libur operasional yang memengaruhi proses laundry.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_hari_libur')
                            ->label('Nama Hari Libur')
                            ->placeholder('Contoh: Libur Tahun Baru')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Libur')
                            ->options([
                                'nasional' => 'Nasional',
                                'operasional' => 'Operasional',
                                'lainnya' => 'Lainnya',
                            ])
                            ->default('operasional')
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->helperText('Kosongkan jika hanya libur satu hari.')
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Contoh: Laundry tidak beroperasi selama hari libur.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_mulai', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('nama_hari_libur')
                    ->label('Nama Hari Libur')
                    ->description(fn (HariLibur $record): ?string => $record->keterangan)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('periode_libur')
                    ->label('Periode Libur')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('tanggal_mulai', $direction);
                    }),

                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nasional' => 'Nasional',
                        'operasional' => 'Operasional',
                        'lainnya' => 'Lainnya',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'nasional' => 'info',
                        'operasional' => 'warning',
                        'lainnya' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

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
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Filter Jenis Libur')
                    ->options([
                        'nasional' => 'Nasional',
                        'operasional' => 'Operasional',
                        'lainnya' => 'Lainnya',
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
            ->emptyStateHeading('Belum ada data hari libur')
            ->emptyStateDescription('Tambahkan hari libur agar estimasi pengerjaan laundry dapat disesuaikan dengan jadwal operasional.')
            ->emptyStateIcon('heroicon-o-calendar-days');
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
            'index' => Pages\ListHariLiburs::route('/'),
            'create' => Pages\CreateHariLibur::route('/create'),
            'edit' => Pages\EditHariLibur::route('/{record}/edit'),
        ];
    }
}
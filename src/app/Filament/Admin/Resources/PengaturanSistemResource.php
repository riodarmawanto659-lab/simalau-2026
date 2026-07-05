<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PengaturanSistemResource\Pages;
use App\Models\PengaturanSistem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PengaturanSistemResource extends Resource
{
    protected static ?string $model = PengaturanSistem::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Sistem';

    protected static ?string $modelLabel = 'Pengaturan Sistem';

    protected static ?string $pluralModelLabel = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Laundry')
                    ->description('Data utama usaha laundry yang ditampilkan pada sistem dan nota transaksi.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_laundry')
                            ->label('Nama Laundry')
                            ->placeholder('Contoh: LaundryKita')
                            ->required()
                            ->maxLength(255)
                            ->default('LaundryKita'),

                        Forms\Components\TextInput::make('nomor_whatsapp')
                            ->label('Nomor WhatsApp')
                            ->placeholder('Contoh: 081234567890')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Contoh: admin@laundrykita.test')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\Select::make('status_sistem')
                            ->label('Status Sistem')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Jam Operasional')
                    ->description('Atur jam buka dan jam tutup laundry.')
                    ->schema([
                        Forms\Components\TimePicker::make('jam_buka')
                            ->label('Jam Buka')
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\TimePicker::make('jam_tutup')
                            ->label('Jam Tutup')
                            ->seconds(false)
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Alamat dan Lokasi')
                    ->description('Alamat dan koordinat lokasi laundry.')
                    ->schema([
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Laundry')
                            ->placeholder('Masukkan alamat lengkap laundry')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->placeholder('Contoh: -6.914744')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->placeholder('Contoh: 107.609810')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Konten Website dan Nota')
                    ->description('Informasi tambahan untuk halaman website dan cetak nota.')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Laundry')
                            ->placeholder('Tuliskan deskripsi singkat usaha laundry.')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('catatan_nota')
                            ->label('Catatan Nota')
                            ->placeholder('Contoh: Terima kasih telah menggunakan layanan kami.')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Laundry')
                            ->image()
                            ->directory('logo-laundry')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('Format gambar. Maksimal 2 MB.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nama_laundry', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('nama_laundry')
                    ->label('Nama Laundry')
                    ->description(fn (PengaturanSistem $record): ?string => $record->alamat)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nomor_whatsapp')
                    ->label('No. WhatsApp')
                    ->copyable()
                    ->copyMessage('Nomor WhatsApp berhasil disalin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->copyable()
                    ->copyMessage('Email berhasil disalin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jam_operasional')
                    ->label('Jam Operasional'),

                Tables\Columns\TextColumn::make('status_sistem')
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
                Tables\Filters\SelectFilter::make('status_sistem')
                    ->label('Filter Status Sistem')
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
            ->emptyStateHeading('Belum ada pengaturan sistem')
            ->emptyStateDescription('Tambahkan pengaturan sistem untuk mengatur informasi utama laundry.')
            ->emptyStateIcon('heroicon-o-cog-6-tooth');
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
            'index' => Pages\ListPengaturanSistems::route('/'),
            'create' => Pages\CreatePengaturanSistem::route('/create'),
            'edit' => Pages\EditPengaturanSistem::route('/{record}/edit'),
        ];
    }
}
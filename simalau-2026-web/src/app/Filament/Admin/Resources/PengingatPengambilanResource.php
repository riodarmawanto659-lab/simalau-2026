<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PengingatPengambilanResource\Pages;
use App\Models\PengingatPengambilan;
use App\Services\LaundryOrderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengingatPengambilanResource extends Resource
{
    protected static ?string $model = PengingatPengambilan::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Operasional Laundry';

    protected static ?string $navigationLabel = 'Pengingat Pengambilan';

    protected static ?string $modelLabel = 'Pengingat Pengambilan';

    protected static ?string $pluralModelLabel = 'Pengingat Pengambilan';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        app(LaundryOrderService::class)->syncDueReminders();

        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengingat')
                    ->description('Data ini otomatis dibuat saat pelanggan membuat pesanan agar admin dapat langsung menghubungi pelanggan.')
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
                                $pesanan = \App\Models\Pesanan::with('pelanggan')->find($state);

                                if (! $pesanan) {
                                    return;
                                }

                                $set('pelanggan_id', $pesanan->pelanggan_id);
                                $set('tanggal_siap_diambil', $pesanan->tanggal_siap_diambil);
                            }),

                        Forms\Components\Select::make('pelanggan_id')
                            ->label('Pelanggan')
                            ->relationship('pelanggan', 'nama_lengkap')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('tanggal_siap_diambil')
                            ->label('Tanggal Siap Diambil')
                            ->helperText('Opsional. Otomatis terisi saat status cucian menjadi Siap Diambil.')
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\DateTimePicker::make('tanggal_masuk_pengingat')
                            ->label('Tanggal Masuk Daftar')
                            ->helperText('Tanggal saat pesanan otomatis masuk daftar pengingat kontak.')
                            ->required()
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\TextInput::make('jumlah_hari_tertahan')
                            ->label('Hari Setelah Siap Diambil')
                            ->suffix('hari')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(3),

                        Forms\Components\Select::make('status_pengingat')
                            ->label('Status Pengingat')
                            ->options([
                                'aktif' => 'Aktif',
                                'sudah_dihubungi' => 'Sudah Dihubungi',
                                'selesai' => 'Selesai',
                            ])
                            ->default('aktif')
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('tanggal_dihubungi')
                            ->label('Tanggal Dihubungi')
                            ->helperText('Diisi jika pelanggan sudah dihubungi oleh admin.')
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Pengingat')
                            ->placeholder('Contoh: Pelanggan sudah dihubungi melalui WhatsApp.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_masuk_pengingat', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('pesanan.nomor_pesanan')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor pesanan berhasil disalin'),

                Tables\Columns\TextColumn::make('pelanggan.nama_lengkap')
                    ->label('Pelanggan')
                    ->description(fn (PengingatPengambilan $record): ?string => $record->pelanggan?->nomor_whatsapp)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pesanan.status_pesanan')
                    ->label('Status Pesanan')
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
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'menunggu_konfirmasi' => 'gray',
                        'menunggu_proses' => 'warning',
                        'sedang_dicuci', 'sedang_dikeringkan', 'sedang_disetrika' => 'info',
                        'siap_diambil', 'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_siap_diambil')
                    ->label('Siap Diambil')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum siap')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_masuk_pengingat')
                    ->label('Masuk Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_hari_tertahan')
                    ->label('Hari Siap')
                    ->formatStateUsing(fn ($state): string => $state . ' hari')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_pengingat')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'sudah_dihubungi' => 'Sudah Dihubungi',
                        'selesai' => 'Selesai',
                        default => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'warning',
                        'sudah_dihubungi' => 'info',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_dihubungi')
                    ->label('Tanggal Dihubungi')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum dihubungi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pengingat')
                    ->label('Filter Status Pengingat')
                    ->options([
                        'aktif' => 'Aktif',
                        'sudah_dihubungi' => 'Sudah Dihubungi',
                        'selesai' => 'Selesai',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('hubungi_whatsapp')
                    ->label('Hubungi Pengguna')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (PengingatPengambilan $record): ?string => $record->whatsapp_url)
                    ->openUrlInNewTab()
                    ->visible(fn (PengingatPengambilan $record): bool => filled($record->whatsapp_url)),

                Tables\Actions\Action::make('tandai_dihubungi')
                    ->label('Tandai Dihubungi')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (PengingatPengambilan $record): bool => $record->status_pengingat === 'aktif')
                    ->action(function (PengingatPengambilan $record): void {
                        $record->update([
                            'status_pengingat' => 'sudah_dihubungi',
                            'tanggal_dihubungi' => now(),
                            'catatan' => trim(($record->catatan ? $record->catatan . PHP_EOL : '') . 'Pelanggan sudah dihubungi oleh admin.'),
                        ]);
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
            ->emptyStateHeading('Belum ada pengingat pengambilan')
            ->emptyStateDescription('Setiap pesanan baru otomatis masuk ke halaman ini agar admin dapat langsung menghubungi pelanggan.')
            ->emptyStateIcon('heroicon-o-bell-alert');
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
            'index' => Pages\ListPengingatPengambilans::route('/'),
            'edit' => Pages\EditPengingatPengambilan::route('/{record}/edit'),
        ];
    }
}

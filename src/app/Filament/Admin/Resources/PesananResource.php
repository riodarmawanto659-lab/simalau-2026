<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PesananResource\Pages;
use App\Filament\Admin\Resources\PesananResource\RelationManagers;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pelanggan_id')
                    ->relationship('pelanggan', 'id')
                    ->required(),
                Forms\Components\TextInput::make('nomor_pesanan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('tanggal_masuk'),
                Forms\Components\DateTimePicker::make('estimasi_selesai'),
                Forms\Components\DateTimePicker::make('tanggal_siap_diambil'),
                Forms\Components\DateTimePicker::make('tanggal_selesai'),
                Forms\Components\TextInput::make('metode_penyerahan')
                    ->required(),
                Forms\Components\Textarea::make('alamat_penjemputan')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('catatan_pelanggan')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('catatan_admin')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status_pesanan')
                    ->required(),
                Forms\Components\TextInput::make('status_pembayaran')
                    ->required(),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('diskon')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('total_biaya')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('urutan_antrian')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_pesanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimasi_selesai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_siap_diambil')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_penyerahan'),
                Tables\Columns\TextColumn::make('status_pesanan'),
                Tables\Columns\TextColumn::make('status_pembayaran'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('diskon')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_biaya')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('urutan_antrian')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

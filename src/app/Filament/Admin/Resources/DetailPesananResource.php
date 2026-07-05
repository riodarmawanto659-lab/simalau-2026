<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DetailPesananResource\Pages;
use App\Filament\Admin\Resources\DetailPesananResource\RelationManagers;
use App\Models\DetailPesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailPesananResource extends Resource
{
    protected static ?string $model = DetailPesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pesanan_id')
                    ->relationship('pesanan', 'id')
                    ->required(),
                Forms\Components\Select::make('layanan_laundry_id')
                    ->relationship('layananLaundry', 'id')
                    ->required(),
                Forms\Components\TextInput::make('nama_layanan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tipe_layanan')
                    ->required(),
                Forms\Components\TextInput::make('berat')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('jumlah_item')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('satuan_hitung')
                    ->required()
                    ->maxLength(255)
                    ->default('kg'),
                Forms\Components\TextInput::make('harga_satuan')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pesanan.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('layananLaundry.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_layanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe_layanan'),
                Tables\Columns\TextColumn::make('berat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_item')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('satuan_hitung')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
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
            'index' => Pages\ListDetailPesanans::route('/'),
            'create' => Pages\CreateDetailPesanan::route('/create'),
            'edit' => Pages\EditDetailPesanan::route('/{record}/edit'),
        ];
    }
}

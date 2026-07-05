<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LayananLaundryResource\Pages;
use App\Filament\Admin\Resources\LayananLaundryResource\RelationManagers;
use App\Models\LayananLaundry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LayananLaundryResource extends Resource
{
    protected static ?string $model = LayananLaundry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kategori_layanan_id')
                    ->relationship('kategoriLayanan', 'id')
                    ->required(),
                Forms\Components\TextInput::make('nama_layanan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tipe_layanan')
                    ->required(),
                Forms\Components\TextInput::make('tarif')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('estimasi_hari')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('minimal_order')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('satuan_hitung')
                    ->required()
                    ->maxLength(255)
                    ->default('kg'),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategoriLayanan.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_layanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe_layanan'),
                Tables\Columns\TextColumn::make('tarif')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimasi_hari')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimal_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('satuan_hitung')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListLayananLaundries::route('/'),
            'create' => Pages\CreateLayananLaundry::route('/create'),
            'edit' => Pages\EditLayananLaundry::route('/{record}/edit'),
        ];
    }
}

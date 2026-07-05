<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ArusKasResource\Pages;
use App\Filament\Admin\Resources\ArusKasResource\RelationManagers;
use App\Models\ArusKas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArusKasResource extends Resource
{
    protected static ?string $model = ArusKas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListArusKas::route('/'),
            'create' => Pages\CreateArusKas::route('/create'),
            'edit' => Pages\EditArusKas::route('/{record}/edit'),
        ];
    }
}

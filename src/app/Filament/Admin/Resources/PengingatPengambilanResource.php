<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PengingatPengambilanResource\Pages;
use App\Filament\Admin\Resources\PengingatPengambilanResource\RelationManagers;
use App\Models\PengingatPengambilan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengingatPengambilanResource extends Resource
{
    protected static ?string $model = PengingatPengambilan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pesanan_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pelanggan_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('tanggal_siap_diambil')
                    ->required(),
                Forms\Components\DateTimePicker::make('tanggal_masuk_pengingat')
                    ->required(),
                Forms\Components\TextInput::make('jumlah_hari_tertahan')
                    ->required()
                    ->numeric()
                    ->default(3),
                Forms\Components\TextInput::make('status_pengingat')
                    ->required(),
                Forms\Components\DateTimePicker::make('tanggal_dihubungi'),
                Forms\Components\Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pesanan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pelanggan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_siap_diambil')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_masuk_pengingat')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_hari_tertahan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_pengingat'),
                Tables\Columns\TextColumn::make('tanggal_dihubungi')
                    ->dateTime()
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
            'index' => Pages\ListPengingatPengambilans::route('/'),
            'create' => Pages\CreatePengingatPengambilan::route('/create'),
            'edit' => Pages\EditPengingatPengambilan::route('/{record}/edit'),
        ];
    }
}

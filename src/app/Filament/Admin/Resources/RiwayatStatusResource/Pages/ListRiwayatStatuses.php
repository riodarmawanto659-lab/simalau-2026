<?php

namespace App\Filament\Admin\Resources\RiwayatStatusResource\Pages;

use App\Filament\Admin\Resources\RiwayatStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatStatuses extends ListRecords
{
    protected static string $resource = RiwayatStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

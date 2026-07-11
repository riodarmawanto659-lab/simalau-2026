<?php

namespace App\Filament\Admin\Resources\ArusKasResource\Pages;

use App\Filament\Admin\Resources\ArusKasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArusKas extends ListRecords
{
    protected static string $resource = ArusKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\HariLibuResource\Pages;

use App\Filament\Admin\Resources\HariLibuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHariLibu extends EditRecord
{
    protected static string $resource = HariLibuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

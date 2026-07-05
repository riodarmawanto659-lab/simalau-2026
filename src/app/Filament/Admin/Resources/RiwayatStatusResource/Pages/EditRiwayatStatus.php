<?php

namespace App\Filament\Admin\Resources\RiwayatStatusResource\Pages;

use App\Filament\Admin\Resources\RiwayatStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatStatus extends EditRecord
{
    protected static string $resource = RiwayatStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\LayananLaundryResource\Pages;

use App\Filament\Admin\Resources\LayananLaundryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayananLaundry extends EditRecord
{
    protected static string $resource = LayananLaundryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\PengaturanSistemResource\Pages;

use App\Filament\Admin\Resources\PengaturanSistemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanSistem extends EditRecord
{
    protected static string $resource = PengaturanSistemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

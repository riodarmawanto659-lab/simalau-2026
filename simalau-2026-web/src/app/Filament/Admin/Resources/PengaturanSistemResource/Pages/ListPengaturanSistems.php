<?php

namespace App\Filament\Admin\Resources\PengaturanSistemResource\Pages;

use App\Filament\Admin\Resources\PengaturanSistemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanSistems extends ListRecords
{
    protected static string $resource = PengaturanSistemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

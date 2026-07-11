<?php

namespace App\Filament\Admin\Resources\PengingatPengambilanResource\Pages;

use App\Filament\Admin\Resources\PengingatPengambilanResource;
use Filament\Resources\Pages\ListRecords;

class ListPengingatPengambilans extends ListRecords
{
    protected static string $resource = PengingatPengambilanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

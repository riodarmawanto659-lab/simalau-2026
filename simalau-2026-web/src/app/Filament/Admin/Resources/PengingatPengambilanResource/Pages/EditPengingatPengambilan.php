<?php

namespace App\Filament\Admin\Resources\PengingatPengambilanResource\Pages;

use App\Filament\Admin\Resources\PengingatPengambilanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengingatPengambilan extends EditRecord
{
    protected static string $resource = PengingatPengambilanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

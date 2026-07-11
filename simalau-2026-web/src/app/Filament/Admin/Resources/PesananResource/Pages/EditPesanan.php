<?php

namespace App\Filament\Admin\Resources\PesananResource\Pages;

use App\Filament\Admin\Resources\PesananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (
            $this->record->status_pesanan === 'menunggu_konfirmasi'
            && ($data['status_pesanan'] ?? 'menunggu_konfirmasi') !== 'menunggu_konfirmasi'
            && $this->record->status_pembayaran !== 'lunas'
        ) {
            throw ValidationException::withMessages([
                'status_pesanan' => 'Pesanan belum bisa dikonfirmasi sebelum pembayaran QRIS diverifikasi.',
            ]);
        }

        return $data;
    }
}

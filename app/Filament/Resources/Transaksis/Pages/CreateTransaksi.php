<?php

namespace App\Filament\Resources\Transaksis\Pages;

use App\Filament\Resources\Transaksis\TransaksiResource;
use App\Models\Transaksi;
use DomainException;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    protected static bool $canCreateAnother = false;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Kendaraan berhasil dicatat masuk';
    }

    // Tarif otomatis, area terisi bertambah, dan petugas dicatat di model Transaksi.
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return Transaksi::catatMasuk($data);
        } catch (DomainException $e) {
            Notification::make()->title('Gagal mencatat kendaraan masuk')->body($e->getMessage())->danger()->send();

            $this->halt();

            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

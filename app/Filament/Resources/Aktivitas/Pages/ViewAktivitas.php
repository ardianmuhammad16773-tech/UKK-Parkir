<?php

namespace App\Filament\Resources\Aktivitas\Pages;

use App\Filament\Resources\Aktivitas\AktivitasResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAktivitas extends ViewRecord
{
    protected static string $resource = AktivitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

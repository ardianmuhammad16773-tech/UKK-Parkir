<?php

namespace App\Filament\Resources\Parkirs\Pages;

use App\Filament\Resources\Parkirs\ParkirResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewParkir extends ViewRecord
{
    protected static string $resource = ParkirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

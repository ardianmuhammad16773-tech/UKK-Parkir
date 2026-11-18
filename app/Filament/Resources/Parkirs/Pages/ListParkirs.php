<?php

namespace App\Filament\Resources\Parkirs\Pages;

use App\Filament\Resources\Parkirs\ParkirResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParkirs extends ListRecords
{
    protected static string $resource = ParkirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Parkirs;

use App\Filament\Resources\Parkirs\Pages\CreateParkir;
use App\Filament\Resources\Parkirs\Pages\EditParkir;
use App\Filament\Resources\Parkirs\Pages\ListParkirs;
use App\Filament\Resources\Parkirs\Pages\ViewParkir;
use App\Filament\Resources\Parkirs\Schemas\ParkirForm;
use App\Filament\Resources\Parkirs\Schemas\ParkirInfolist;
use App\Filament\Resources\Parkirs\Tables\ParkirsTable;
use App\Models\Parkir;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParkirResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static ?string $recordTitleAttribute = 'AreaParkir';

    public static function form(Schema $schema): Schema
    {
        return ParkirForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ParkirInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParkirsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParkirs::route('/'),
            'create' => CreateParkir::route('/create'),
            'view' => ViewParkir::route('/{record}'),
            'edit' => EditParkir::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

<?php

namespace App\Filament\Resources\Aktivitas;

use App\Filament\Resources\Aktivitas\Pages\CreateAktivitas;
use App\Filament\Resources\Aktivitas\Pages\EditAktivitas;
use App\Filament\Resources\Aktivitas\Pages\ListAktivitas;
use App\Filament\Resources\Aktivitas\Pages\ViewAktivitas;
use App\Filament\Resources\Aktivitas\Schemas\AktivitasForm;
use App\Filament\Resources\Aktivitas\Schemas\AktivitasInfolist;
use App\Filament\Resources\Aktivitas\Tables\AktivitasTable;
use App\Models\Aktivitas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AktivitasResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmark;

    protected static ?string $recordTitleAttribute = 'log_aktivitas';

    public static function form(Schema $schema): Schema
    {
        return AktivitasForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AktivitasInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AktivitasTable::configure($table);
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
            'index' => ListAktivitas::route('/'),
            'create' => CreateAktivitas::route('/create'),
            'view' => ViewAktivitas::route('/{record}'),
            'edit' => EditAktivitas::route('/{record}/edit'),
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

<?php

namespace App\Filament\Resources\Tarifs;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\Tarifs\Pages\CreateTarif;
use App\Filament\Resources\Tarifs\Pages\EditTarif;
use App\Filament\Resources\Tarifs\Pages\ListTarifs;
use App\Models\Tarif;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TarifResource extends Resource
{
    use RestrictedToRoles;

    // CRUD Tarif Parkir: hanya Admin
    protected static array $roles = ['admin'];

    protected static ?string $model = Tarif::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $modelLabel = 'Tarif Parkir';

    protected static ?string $pluralModelLabel = 'Tarif Parkir';

    protected static ?int $navigationSort = 2;

    public static function canDelete(Model $record): bool
    {
        // Tarif yang sudah dipakai transaksi tidak boleh dihapus.
        return static::allowed() && ! $record->transaksi()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('jenis_kendaraan')
                ->label('Jenis Kendaraan')
                ->options(['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'])
                ->required()
                ->unique(ignoreRecord: true)
                ->validationMessages(['unique' => 'Tarif untuk jenis ini sudah ada, silakan edit yang lama.']),
            TextInput::make('tarif_per_jam')
                ->label('Tarif per Jam')
                ->prefix('Rp')
                ->numeric()
                ->minValue(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis_kendaraan')->label('Jenis')->badge()->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('tarif_per_jam')->label('Tarif / Jam')->money('IDR', locale: 'id'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTarifs::route('/'),
            'create' => CreateTarif::route('/create'),
            'edit' => EditTarif::route('/{record}/edit'),
        ];
    }
}

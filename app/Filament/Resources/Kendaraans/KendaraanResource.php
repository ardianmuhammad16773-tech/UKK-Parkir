<?php

namespace App\Filament\Resources\Kendaraans;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\Kendaraans\Pages\CreateKendaraan;
use App\Filament\Resources\Kendaraans\Pages\EditKendaraan;
use App\Filament\Resources\Kendaraans\Pages\ListKendaraans;
use App\Models\Kendaraan;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class KendaraanResource extends Resource
{
    use RestrictedToRoles;

    // CRUD Kendaraan: hanya Admin
    protected static array $roles = ['admin'];

    protected static ?string $model = Kendaraan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $modelLabel = 'Kendaraan';

    protected static ?string $pluralModelLabel = 'Kendaraan';

    protected static ?string $recordTitleAttribute = 'plat_nomor';

    protected static ?int $navigationSort = 4;

    public static function canDelete(Model $record): bool
    {
        // Kendaraan yang sudah punya riwayat transaksi tidak boleh dihapus.
        return static::allowed() && ! $record->transaksi()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('plat_nomor')
                ->label('Plat Nomor')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true)
                ->validationMessages(['unique' => 'Plat nomor ini sudah terdaftar.']),
            Select::make('jenis_kendaraan')
                ->label('Jenis Kendaraan')
                ->options(['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'])
                ->required(),
            TextInput::make('warna')->maxLength(50),
            TextInput::make('pemilik')->required()->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plat_nomor')->label('Plat Nomor')->searchable(),
                TextColumn::make('jenis_kendaraan')->label('Jenis')->badge()->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('warna')->placeholder('-'),
                TextColumn::make('pemilik')->searchable(),
            ])
            ->filters([
                SelectFilter::make('jenis_kendaraan')
                    ->label('Jenis')
                    ->options(['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya']),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKendaraans::route('/'),
            'create' => CreateKendaraan::route('/create'),
            'edit' => EditKendaraan::route('/{record}/edit'),
        ];
    }
}

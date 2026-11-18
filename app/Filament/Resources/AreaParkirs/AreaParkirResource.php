<?php

namespace App\Filament\Resources\AreaParkirs;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\AreaParkirs\Pages\CreateAreaParkir;
use App\Filament\Resources\AreaParkirs\Pages\EditAreaParkir;
use App\Filament\Resources\AreaParkirs\Pages\ListAreaParkirs;
use App\Models\AreaParkir;
use BackedEnum;
use Closure;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AreaParkirResource extends Resource
{
    use RestrictedToRoles;

    // CRUD Area Parkir: hanya Admin
    protected static array $roles = ['admin'];

    protected static ?string $model = AreaParkir::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $modelLabel = 'Area Parkir';

    protected static ?string $pluralModelLabel = 'Area Parkir';

    protected static ?string $slug = 'area-parkir';

    protected static ?int $navigationSort = 3;

    public static function canDelete(Model $record): bool
    {
        return static::allowed() && ! $record->transaksi()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nama_area')->label('Nama Area')->required()->maxLength(50),
            TextInput::make('kapasitas')
                ->numeric()
                ->minValue(1)
                ->required()
                ->rules([
                    fn (?Model $record): Closure => function (string $attribute, $value, Closure $fail) use ($record) {
                        if ($record && (int) $value < $record->terisi) {
                            $fail("Kapasitas tidak boleh kurang dari kendaraan yang sedang parkir ({$record->terisi}).");
                        }
                    },
                ]),
            TextInput::make('terisi')
                ->label('Terisi')
                ->numeric()
                ->default(0)
                ->disabled()
                ->dehydrated(false)
                ->helperText('Terisi otomatis dari transaksi masuk dan keluar.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_area')->label('Area')->searchable(),
                TextColumn::make('kapasitas'),
                TextColumn::make('terisi'),
                TextColumn::make('sisa')
                    ->label('Sisa')
                    ->state(fn (AreaParkir $record): int => $record->kapasitas - $record->terisi),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAreaParkirs::route('/'),
            'create' => CreateAreaParkir::route('/create'),
            'edit' => EditAreaParkir::route('/{record}/edit'),
        ];
    }
}

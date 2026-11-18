<?php

namespace App\Filament\Resources\Users;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    use RestrictedToRoles;

    // CRUD User: hanya Admin
    protected static array $roles = ['admin'];

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'User';

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    protected static ?int $navigationSort = 1;

    public static function canDelete(Model $record): bool
    {
        // Admin tidak boleh menghapus akunnya sendiri.
        return static::allowed() && $record->getKey() !== auth()->id();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nama_lengkap')->label('Nama Lengkap')->required()->maxLength(100),
            TextInput::make('username')->required()->maxLength(50)->unique(ignoreRecord: true),
            TextInput::make('password')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->minLength(6)
                ->helperText('Kosongkan saat edit jika tidak ingin mengganti password.'),
            Select::make('role')->options(User::ROLES)->required()->default('petugas'),
            Toggle::make('status_aktif')
                ->label('Akun aktif')
                ->default(true)
                // Admin tidak boleh menonaktifkan akunnya sendiri.
                ->disabled(fn (?Model $record): bool => $record?->getKey() === auth()->id())
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_lengkap')->label('Nama')->searchable(),
                TextColumn::make('username')->searchable(),
                TextColumn::make('role')->badge()->formatStateUsing(fn (string $state): string => User::ROLES[$state] ?? $state),
                IconColumn::make('status_aktif')->label('Aktif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('role')->options(User::ROLES),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}

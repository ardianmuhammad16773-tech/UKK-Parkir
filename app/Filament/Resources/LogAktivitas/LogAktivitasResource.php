<?php

namespace App\Filament\Resources\LogAktivitas;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\LogAktivitas\Pages\ListLogAktivitas;
use App\Models\LogAktivitas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LogAktivitasResource extends Resource
{
    use RestrictedToRoles;

    // Akses Log Aktivitas: hanya Admin
    protected static array $roles = ['admin'];

    protected static ?string $model = LogAktivitas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Log Aktivitas';

    protected static ?string $slug = 'log-aktivitas';

    protected static ?int $navigationSort = 5;

    // Log hanya boleh dibaca.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_aktivitas', 'desc')
            ->columns([
                TextColumn::make('waktu_aktivitas')->label('Waktu')->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('user.nama_lengkap')->label('User')->placeholder('(user dihapus)')->searchable(),
                TextColumn::make('user.role')->label('Role')->badge()->placeholder('-'),
                TextColumn::make('aktivitas')->label('Aktivitas')->wrap()->searchable(),
            ])
            ->filters([
                SelectFilter::make('id_user')->label('User')->relationship('user', 'nama_lengkap'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogAktivitas::route('/'),
        ];
    }
}

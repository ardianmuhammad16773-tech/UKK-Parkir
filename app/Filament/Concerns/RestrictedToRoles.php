<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Membatasi resource berdasarkan role (sesuai matriks fitur).
 * Resource wajib mengisi: protected static array $roles = ['admin'];
 */
trait RestrictedToRoles
{
    protected static function allowed(): bool
    {
        return auth()->user()?->hasRole(...static::$roles) ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::allowed();
    }

    public static function canView(Model $record): bool
    {
        return static::allowed();
    }

    public static function canCreate(): bool
    {
        return static::allowed();
    }

    public static function canEdit(Model $record): bool
    {
        return static::allowed();
    }

    public static function canDelete(Model $record): bool
    {
        return static::allowed();
    }

    public static function canDeleteAny(): bool
    {
        return static::allowed();
    }
}

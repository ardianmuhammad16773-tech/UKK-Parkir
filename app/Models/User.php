<?php

namespace App\Models;

use App\Models\Concerns\MencatatLog;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use MencatatLog, Notifiable;

    public const ROLES = [
        'admin' => 'Admin',
        'petugas' => 'Petugas',
        'owner' => 'Owner',
    ];

    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';

    protected $fillable = ['nama_lengkap', 'username', 'password', 'role', 'status_aktif'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status_aktif && array_key_exists((string) $this->role, self::ROLES);
    }

    public function getFilamentName(): string
    {
        return $this->nama_lengkap;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}

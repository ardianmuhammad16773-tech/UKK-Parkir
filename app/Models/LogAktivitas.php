<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LogAktivitas extends Model
{
    protected $table = 'tb_log_aktivitas';
    protected $primaryKey = 'id_log';
    public $timestamps = false;
    protected $fillable = ['id_user', 'aktivitas', 'waktu_aktivitas'];

    protected $casts = ['waktu_aktivitas' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /** Catat aktivitas user yang sedang login (atau $idUser kalau diberikan). */
    public static function catat(string $aktivitas, ?int $idUser = null): void
    {
        $idUser ??= auth()->id();

        if (! $idUser) {
            return; // tidak ada user login (misal saat seeding)
        }

        static::create([
            'id_user' => $idUser,
            'aktivitas' => Str::limit($aktivitas, 97),
            'waktu_aktivitas' => now(),
        ]);
    }
}

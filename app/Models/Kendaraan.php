<?php

namespace App\Models;

use App\Models\Concerns\MencatatLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kendaraan extends Model
{
    use MencatatLog;

    protected $table = 'tb_kendaraan';
    protected $primaryKey = 'id_kendaraan';

    protected $fillable = ['plat_nomor', 'jenis_kendaraan', 'warna', 'pemilik', 'id_user'];

    protected static function booted(): void
    {
        // Catat user (admin) yang menambahkan kendaraan.
        static::creating(fn (Kendaraan $k) => $k->id_user ??= auth()->id());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_kendaraan', 'id_kendaraan');
    }
}

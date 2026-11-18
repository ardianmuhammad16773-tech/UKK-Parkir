<?php

namespace App\Models;

use App\Models\Concerns\MencatatLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    use MencatatLog;

    protected $table = 'tb_tarif';
    protected $primaryKey = 'id_tarif';
    public $timestamps = false; // tabel tb_tarif tidak punya created_at/updated_at

    protected $fillable = ['jenis_kendaraan', 'tarif_per_jam'];

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_tarif', 'id_tarif');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Transaksi extends Model
{
    protected $table = 'tb_transaksi';
    protected $primaryKey = 'id_parkir';

    protected $fillable = [
        'id_kendaraan', 'waktu_masuk', 'waktu_keluar', 'id_tarif',
        'durasi_jam', 'biaya_total', 'status', 'id_user', 'id_area',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class, 'id_tarif', 'id_tarif');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(AreaParkir::class, 'id_area', 'id_area');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /** Durasi (jam, dibulatkan ke atas, minimal 1) dan biaya sampai $sampai. */
    public function hitungBiaya(?Carbon $sampai = null): array
    {
        $sampai ??= now();
        $menit = max(0, $this->waktu_masuk->diffInMinutes($sampai));
        $jam = max(1, (int) ceil($menit / 60));

        return ['jam' => $jam, 'biaya' => $jam * (int) $this->tarif->tarif_per_jam];
    }

    /** Catat kendaraan masuk. Tarif otomatis dari jenis kendaraan. */
    public static function catatMasuk(array $data): self
    {
        $kendaraan = Kendaraan::findOrFail($data['id_kendaraan']);

        $tarif = Tarif::where('jenis_kendaraan', $kendaraan->jenis_kendaraan)->first();

        if (! $tarif) {
            throw new DomainException("Tarif untuk jenis {$kendaraan->jenis_kendaraan} belum diatur admin.");
        }

        $transaksi = DB::transaction(function () use ($data, $kendaraan, $tarif) {
            $area = AreaParkir::lockForUpdate()->findOrFail($data['id_area']);

            if ($area->terisi >= $area->kapasitas) {
                throw new DomainException("{$area->nama_area} sudah penuh.");
            }

            if (static::where('id_kendaraan', $kendaraan->id_kendaraan)->where('status', 'masuk')->exists()) {
                throw new DomainException('Kendaraan ini masih tercatat parkir.');
            }

            $transaksi = static::create([
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'waktu_masuk' => now(),
                'id_tarif' => $tarif->id_tarif,
                'status' => 'masuk',
                'id_user' => auth()->id(),
                'id_area' => $area->id_area,
            ]);

            AreaParkir::whereKey($area->id_area)->increment('terisi');

            return $transaksi;
        });

        LogAktivitas::catat('Kendaraan masuk: ' . $kendaraan->plat_nomor);

        return $transaksi;
    }

    /** Catat kendaraan keluar, hitung biaya, kosongkan slot area. */
    public function catatKeluar(): void
    {
        DB::transaction(function () {
            $fresh = static::with('tarif')->lockForUpdate()->findOrFail($this->getKey());

            if ($fresh->status !== 'masuk') {
                throw new DomainException('Transaksi ini sudah selesai.');
            }

            $keluar = now();
            $hasil = $fresh->hitungBiaya($keluar);

            $fresh->update([
                'waktu_keluar' => $keluar,
                'durasi_jam' => $hasil['jam'],
                'biaya_total' => $hasil['biaya'],
                'status' => 'keluar',
            ]);

            AreaParkir::whereKey($fresh->id_area)->where('terisi', '>', 0)->decrement('terisi');
        });

        $this->refresh();

        LogAktivitas::catat('Kendaraan keluar: ' . $this->kendaraan->plat_nomor);
    }
}

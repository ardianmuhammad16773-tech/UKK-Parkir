<?php

namespace Database\Seeders;

use App\Models\Tarif;
use Illuminate\Database\Seeder;

class TarifSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh nilai, ubah lewat menu Tarif Parkir (admin).
        foreach (['motor' => 2000, 'mobil' => 5000, 'lainnya' => 10000] as $jenis => $tarif) {
            Tarif::updateOrCreate(['jenis_kendaraan' => $jenis], ['tarif_per_jam' => $tarif]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use Illuminate\Database\Seeder;

class AreaParkirSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Area A' => 20, 'Area B' => 10] as $nama => $kapasitas) {
            AreaParkir::updateOrCreate(['nama_area' => $nama], ['kapasitas' => $kapasitas]);
        }
    }
}

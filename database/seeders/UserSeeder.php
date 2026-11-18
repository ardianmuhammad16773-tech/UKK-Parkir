<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Password semua akun: "password" (di-hash otomatis oleh model User)
        $akun = [
            ['Administrator', 'admin', 'admin'],
            ['Petugas Parkir', 'petugas', 'petugas'],
            ['Pemilik Usaha', 'owner', 'owner'],
        ];

        foreach ($akun as [$nama, $username, $role]) {
            User::updateOrCreate(
                ['username' => $username],
                ['nama_lengkap' => $nama, 'password' => 'password', 'role' => $role, 'status_aktif' => true],
            );
        }
    }
}

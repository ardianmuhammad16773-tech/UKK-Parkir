# Perbaikan UKK-Parkir (Filament) sesuai matriks fitur

## Cara pasang (PowerShell, di folder proyek)

1. HAPUS resource lama yang tidak terpakai (model Parkir/Aktivitas tidak ada):

       Remove-Item -Recurse -Force app\Filament\Resources

2. Salin seluruh isi folder `UKK-Parkir` dari zip ini ke proyekmu (pilih Replace).

3. Buat database kosong sesuai `.env` (DB_DATABASE=ukk_2027) lalu:

       php artisan migrate:fresh --seed
       php artisan optimize:clear
       php artisan serve

4. Buka http://127.0.0.1:8000/admin
   Login memakai USERNAME (bukan email). Password semua akun: `password`

| Role    | Username | Menu yang tampil |
|---------|----------|------------------|
| Admin   | admin    | User, Tarif Parkir, Area Parkir, Kendaraan, Log Aktivitas |
| Petugas | petugas  | Transaksi (Kendaraan Masuk, Kendaraan Keluar, Cetak Struk) |
| Owner   | owner    | Rekap Transaksi |

## Matriks fitur

| Fitur               | Admin | Petugas | Owner |
|---------------------|:-----:|:-------:|:-----:|
| Login/Logout        |   v   |    v    |   v   |
| CRUD User           |   v   |         |       |
| CRUD Tarif Parkir   |   v   |         |       |
| CRUD Area Parkir    |   v   |         |       |
| CRUD Kendaraan      |   v   |         |       |
| Akses Log Aktivitas |   v   |         |       |
| Cetak Struk Parkir  |       |    v    |       |
| Transaksi           |       |    v    |       |
| Rekap Transaksi     |       |         |   v   |

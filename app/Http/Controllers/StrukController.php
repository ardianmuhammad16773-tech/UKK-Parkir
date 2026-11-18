<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Transaksi;

class StrukController extends Controller
{
    public function cetak(Transaksi $transaksi)
    {
        // Cetak Struk Parkir: hanya Petugas yang sudah login.
        abort_unless(auth()->check() && auth()->user()->hasRole('petugas'), 403);

        // Struk hanya untuk transaksi yang sudah selesai.
        abort_unless($transaksi->status === 'keluar', 404);

        $transaksi->load(['kendaraan', 'tarif', 'area', 'petugas']);

        LogAktivitas::catat('Cetak struk: ' . $transaksi->kendaraan->plat_nomor);

        return view('struk.cetak', compact('transaksi'));
    }
}

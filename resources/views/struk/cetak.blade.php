<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Parkir #{{ $transaksi->id_parkir }}</title>
    <style>
        body { font-family: "Courier New", monospace; font-size: 13px; width: 280px; margin: 0 auto; padding: 12px; }
        h2, p { text-align: center; margin: 2px 0; }
        hr { border: 0; border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        td:last-child { text-align: right; }
        .total td { font-weight: bold; font-size: 15px; }
        .tombol { text-align: center; margin-top: 14px; }
        @media print { .tombol { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h2>STRUK PARKIR</h2>
    <p>No. {{ str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
    <hr>
    <table>
        <tr><td>Plat Nomor</td><td>{{ $transaksi->kendaraan->plat_nomor }}</td></tr>
        <tr><td>Jenis</td><td>{{ ucfirst($transaksi->kendaraan->jenis_kendaraan) }}</td></tr>
        <tr><td>Area</td><td>{{ $transaksi->area->nama_area }}</td></tr>
        <tr><td>Masuk</td><td>{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Keluar</td><td>{{ $transaksi->waktu_keluar->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Durasi</td><td>{{ $transaksi->durasi_jam }} jam</td></tr>
        <tr><td>Tarif/jam</td><td>Rp {{ number_format($transaksi->tarif->tarif_per_jam, 0, ',', '.') }}</td></tr>
    </table>
    <hr>
    <table>
        <tr class="total"><td>TOTAL</td><td>Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</td></tr>
    </table>
    <hr>
    <p>Petugas: {{ $transaksi->petugas?->nama_lengkap ?? '-' }}</p>
    <p>Dicetak {{ now()->format('d/m/Y H:i') }}</p>
    <p>Terima kasih</p>
    <div class="tombol">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>

<?php

use App\Http\Controllers\StrukController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

// Cetak struk (hanya Petugas, dicek di controller). Binding memakai primary key id_parkir.
Route::get('/struk/{transaksi}', [StrukController::class, 'cetak'])->name('struk.cetak');

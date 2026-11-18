<?php

namespace App\Models\Concerns;

use App\Models\LogAktivitas;

/**
 * Pasang di model yang CRUD-nya ingin dicatat di log aktivitas.
 * Log hanya tercatat kalau ada user yang sedang login.
 */
trait MencatatLog
{
    protected static function bootMencatatLog(): void
    {
        static::created(fn ($m) => LogAktivitas::catat('Tambah ' . static::labelLog($m)));

        static::updated(function ($m) {
            $kolom = collect(array_keys($m->getChanges()))
                ->reject(fn ($k) => in_array($k, ['updated_at', 'remember_token']))
                ->implode(', ');

            if ($kolom !== '') {
                LogAktivitas::catat('Ubah ' . static::labelLog($m) . " ({$kolom})");
            }
        });

        static::deleted(fn ($m) => LogAktivitas::catat('Hapus ' . static::labelLog($m)));
    }

    protected static function labelLog($model): string
    {
        return class_basename($model) . ' #' . $model->getKey();
    }
}

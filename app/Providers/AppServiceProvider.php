<?php

namespace App\Providers;

use App\Models\LogAktivitas;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Log aktivitas login & logout (semua role).
        Event::listen(Login::class, function (Login $event) {
            LogAktivitas::catat('Login ke sistem', $event->user->getAuthIdentifier());
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                LogAktivitas::catat('Logout dari sistem', $event->user->getAuthIdentifier());
            }
        });
    }
}

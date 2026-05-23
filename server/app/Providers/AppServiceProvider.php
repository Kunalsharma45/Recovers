<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Event;
use App\Listeners\AttachPatientOnFirstLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listener: create Patient on first patient login
        Event::listen(Authenticated::class, AttachPatientOnFirstLogin::class);

        // Temporary slow-query logger (non-production)
        if ($this->app->environment() !== 'production') {
            DB::listen(function (QueryExecuted $query) {
                $thresholdMs = 100; // log queries slower than this (ms)
                if ($query->time > $thresholdMs) {
                    $msg = sprintf(
                        "[%sms] %s | bindings: %s\n",
                        round($query->time, 2),
                        $query->sql,
                        json_encode($query->bindings)
                    );
                    @file_put_contents(storage_path('logs/slow_queries.log'), $msg, FILE_APPEND);
                }
            });
        }
    }
}

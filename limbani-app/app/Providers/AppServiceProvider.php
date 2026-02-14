<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Subtask;
use App\Observers\SubtaskObserver;

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
            // Activar el observador
        Subtask::observe(SubtaskObserver::class);
    }
}

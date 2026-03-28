<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Lib\Mir\MirBlade;
use Illuminate\Pagination\Paginator; // MIRAIE obata@mir-ai.co.jp

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MIRAIE obata@mir-ai.co.jp
        // Blade relativeInclude
        Blade::directive('relativeInclude', function ($args) {
            return MirBlade::relativeInclude(
                $args,
                $this->app
            );
        });

        // MIRAIE obata@mir-ai.co.jp
        Paginator::useBootstrapFive(); // Add this line

    }
}

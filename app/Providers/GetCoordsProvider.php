<?php

namespace App\Providers;

use App\Services\Geo\GetCoordsInterface;
use App\Services\Geo\GetCoordsYandexService;
use Illuminate\Support\ServiceProvider;

class GetCoordsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(GetCoordsInterface::class, GetCoordsYandexService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

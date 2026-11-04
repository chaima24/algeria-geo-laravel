<?php

namespace Chaima24\AlgeriaGeo;

use Illuminate\Support\ServiceProvider;

class AlgeriaGeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/algeria-geo.php', 'algeria-geo');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/algeria-geo.php' => config_path('algeria-geo.php'),
        ], 'algeria-geo-config');
    }
}

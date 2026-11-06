<?php

namespace Chaima24\AlgeriaGeo;

use Chaima24\AlgeriaGeo\Console\InstallCommand;
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

        $migrations = __DIR__.'/../database/migrations';

        $this->publishes([$migrations.'/core' => database_path('migrations')], 'algeria-geo-migrations');
        $this->publishes([$migrations.'/regions' => database_path('migrations')], 'algeria-geo-regions');
        $this->publishes([$migrations.'/dairas' => database_path('migrations')], 'algeria-geo-dairas');

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
        }
    }
}

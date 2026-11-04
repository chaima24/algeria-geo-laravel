<?php

use Chaima24\AlgeriaGeo\AlgeriaGeoServiceProvider;
use Chaima24\AlgeriaGeo\Models\Wilaya;
use Illuminate\Support\ServiceProvider;

it('loads the package config', function () {
    expect(config('algeria-geo.models.wilaya'))->toBe(Wilaya::class);
});

it('lets the app publish the config', function () {
    $paths = ServiceProvider::pathsToPublish(AlgeriaGeoServiceProvider::class, 'algeria-geo-config');

    expect($paths)->toHaveCount(1)
        ->and(array_values($paths)[0])->toBe(config_path('algeria-geo.php'));
});

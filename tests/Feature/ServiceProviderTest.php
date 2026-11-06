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

it('publishes each migration group separately', function (string $tag, string $folder) {
    $paths = ServiceProvider::pathsToPublish(AlgeriaGeoServiceProvider::class, $tag);

    expect($paths)->toHaveCount(1)
        ->and(array_key_first($paths))->toEndWith($folder)
        ->and(array_values($paths)[0])->toBe(database_path('migrations'));
})->with([
    ['algeria-geo-migrations', 'core'],
    ['algeria-geo-regions', 'regions'],
    ['algeria-geo-dairas', 'dairas'],
]);

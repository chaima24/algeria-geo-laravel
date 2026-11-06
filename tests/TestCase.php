<?php

namespace Chaima24\AlgeriaGeo\Tests;

use Chaima24\AlgeriaGeo\AlgeriaGeoServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** The migration folders each test runs. */
    protected array $migrations = ['core', 'regions', 'dairas'];

    protected function getPackageProviders($app): array
    {
        return [AlgeriaGeoServiceProvider::class];
    }

    protected function defineDatabaseMigrations(): void
    {
        foreach ($this->migrations as $folder) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations/'.$folder);
        }
    }
}

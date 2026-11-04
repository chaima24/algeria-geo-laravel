<?php

namespace Chaima24\AlgeriaGeo\Tests;

use Chaima24\AlgeriaGeo\AlgeriaGeoServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [AlgeriaGeoServiceProvider::class];
    }
}

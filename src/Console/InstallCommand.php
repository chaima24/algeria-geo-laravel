<?php

namespace Chaima24\AlgeriaGeo\Console;

use Chaima24\AlgeriaGeo\Database\AlgeriaGeoSeeder;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'algeria-geo:install
        {--regions : Also install regions}
        {--dairas : Also install dairas}
        {--force : Run in production without asking}';

    protected $description = 'Publish the config and migrations, migrate, and seed the data';

    public function handle(): int
    {
        foreach ($this->tags() as $tag) {
            $this->call('vendor:publish', ['--tag' => $tag]);
        }

        $force = ['--force' => $this->option('force')];

        if ($this->call('migrate', $force) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if ($this->call('db:seed', ['--class' => AlgeriaGeoSeeder::class] + $force) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->info('Algeria Geo is installed.');

        return self::SUCCESS;
    }

    protected function tags(): array
    {
        return array_filter([
            'algeria-geo-config',
            'algeria-geo-migrations',
            $this->option('regions') ? 'algeria-geo-regions' : null,
            $this->option('dairas') ? 'algeria-geo-dairas' : null,
        ]);
    }
}

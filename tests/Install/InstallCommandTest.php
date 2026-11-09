<?php

use Chaima24\AlgeriaGeo\Models\Commune;
use Chaima24\AlgeriaGeo\Models\Daira;
use Chaima24\AlgeriaGeo\Models\Region;
use Chaima24\AlgeriaGeo\Models\Wilaya;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

function publishedMigrations(): array
{
    return File::glob(database_path('migrations/2026_01_01_00000*.php'));
}

function removePublishedFiles(): void
{
    File::delete(publishedMigrations());
    File::delete(config_path('algeria-geo.php'));
}

beforeEach(function () {
    removePublishedFiles();
    Artisan::call('db:wipe');
});

afterEach(function () {
    removePublishedFiles();
    Artisan::call('db:wipe');
});

it('installs wilayas and communes only by default', function () {
    $this->artisan('algeria-geo:install')->assertSuccessful();

    expect(Wilaya::count())->toBe(69)
        ->and(Commune::count())->toBe(1541)
        ->and(Schema::hasTable('regions'))->toBeFalse()
        ->and(Schema::hasTable('dairas'))->toBeFalse()
        ->and(publishedMigrations())->toHaveCount(2)
        ->and(File::exists(config_path('algeria-geo.php')))->toBeTrue();
});

it('installs regions and dairas when asked', function () {
    $this->artisan('algeria-geo:install', ['--regions' => true, '--dairas' => true])->assertSuccessful();

    expect(Region::count())->toBe(4)
        ->and(Daira::count())->toBe(551)
        ->and(Wilaya::whereNull('region_id')->count())->toBe(0)
        ->and(Commune::whereNull('daira_id')->count())->toBe(0)
        ->and(Region::find(1)->arabic_name)->toBe('الوسط')
        ->and(publishedMigrations())->toHaveCount(5);
});

it('adds regions and dairas later and keeps edits', function () {
    $this->artisan('algeria-geo:install')->assertSuccessful();

    Wilaya::find(16)->update(['name' => 'Edited']);

    $this->artisan('algeria-geo:install', ['--regions' => true, '--dairas' => true])->assertSuccessful();

    expect(Wilaya::find(16)->name)->toBe('Edited')
        ->and(Wilaya::whereNull('region_id')->count())->toBe(0)
        ->and(Commune::whereNull('daira_id')->count())->toBe(0);
});

it('can run twice without duplicates', function () {
    $this->artisan('algeria-geo:install')->assertSuccessful();
    $this->artisan('algeria-geo:install')->assertSuccessful();

    expect(Wilaya::count())->toBe(69)
        ->and(publishedMigrations())->toHaveCount(2);
});

it('keeps your published config', function () {
    $this->artisan('algeria-geo:install')->assertSuccessful();

    File::put(config_path('algeria-geo.php'), '<?php return ["mine" => true];');

    $this->artisan('algeria-geo:install')->assertSuccessful();

    expect(File::get(config_path('algeria-geo.php')))->toContain('mine');
});

it('can be rolled back', function () {
    $this->artisan('algeria-geo:install', ['--regions' => true, '--dairas' => true])->assertSuccessful();
    $this->artisan('migrate:rollback')->assertSuccessful();

    expect(Schema::hasTable('wilayas'))->toBeFalse()
        ->and(Schema::hasTable('communes'))->toBeFalse()
        ->and(Schema::hasTable('regions'))->toBeFalse()
        ->and(Schema::hasTable('dairas'))->toBeFalse();
});

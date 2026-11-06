<?php

use Chaima24\AlgeriaGeo\Models\Commune;
use Chaima24\AlgeriaGeo\Models\Daira;
use Chaima24\AlgeriaGeo\Models\Region;
use Chaima24\AlgeriaGeo\Models\Wilaya;
use Illuminate\Database\QueryException;

function makeGeography(): array
{
    $region = Region::create(['name' => 'SUD']);
    $wilaya = Wilaya::create(['name' => 'Adrar', 'arabic_name' => 'أدرار', 'region_id' => $region->id]);
    $daira = Daira::create(['wilaya_id' => $wilaya->id, 'name' => 'Adrar', 'arabic_name' => 'أدرار']);
    $commune = Commune::create([
        'wilaya_id' => $wilaya->id,
        'daira_id' => $daira->id,
        'name' => 'Adrar',
        'arabic_name' => 'أدرار',
        'latitude' => 27.87429,
        'longitude' => -0.297222,
    ]);

    return [$region, $wilaya, $daira, $commune];
}

it('links regions, wilayas, dairas and communes', function () {
    [$region, $wilaya, $daira, $commune] = makeGeography();

    expect($wilaya->region->is($region))->toBeTrue()
        ->and($region->wilayas)->toHaveCount(1)
        ->and($region->communes)->toHaveCount(1)
        ->and($wilaya->dairas)->toHaveCount(1)
        ->and($wilaya->communes)->toHaveCount(1)
        ->and($daira->wilaya->is($wilaya))->toBeTrue()
        ->and($daira->communes)->toHaveCount(1)
        ->and($commune->wilaya->is($wilaya))->toBeTrue()
        ->and($commune->daira->is($daira))->toBeTrue();
});

it('allows a wilaya without a region and a commune without a daira', function () {
    $wilaya = Wilaya::create(['name' => 'Adrar', 'arabic_name' => 'أدرار']);
    $commune = Commune::create(['wilaya_id' => $wilaya->id, 'name' => 'Tamest', 'arabic_name' => 'تاماست']);

    expect($wilaya->region)->toBeNull()->and($commune->daira)->toBeNull();
});

it('requires every commune to have a wilaya', function () {
    Commune::create(['name' => 'Tamest', 'arabic_name' => 'تاماست']);
})->throws(QueryException::class);

it('returns coordinates as numbers', function () {
    [, , , $commune] = makeGeography();

    expect($commune->fresh()->latitude)->toBe(27.87429)
        ->and($commune->fresh()->longitude)->toBe(-0.297222);
});

it('uses the app models from the config', function () {
    [, $wilaya, , $commune] = makeGeography();

    config()->set('algeria-geo.models.wilaya', AppProvince::class);
    config()->set('algeria-geo.models.commune', AppTown::class);

    expect($commune->wilaya)->toBeInstanceOf(AppProvince::class)
        ->and($commune->wilaya->communes->first())->toBeInstanceOf(AppTown::class)
        ->and((new AppProvince)->getTable())->toBe('wilayas');
});

class AppProvince extends Wilaya {}

class AppTown extends Commune {}

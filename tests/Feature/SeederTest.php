<?php

use Chaima24\AlgeriaGeo\Database\AlgeriaGeoSeeder;
use Chaima24\AlgeriaGeo\Models\Commune;
use Chaima24\AlgeriaGeo\Models\Daira;
use Chaima24\AlgeriaGeo\Models\Region;
use Chaima24\AlgeriaGeo\Models\Wilaya;
use Illuminate\Support\Facades\DB;

beforeEach(fn () => $this->seed(AlgeriaGeoSeeder::class));

it('adds all the data with its links', function () {
    expect(Region::count())->toBe(4)
        ->and(Wilaya::count())->toBe(69)
        ->and(Daira::count())->toBe(551)
        ->and(Commune::count())->toBe(1541);

    $commune = Commune::find(101);

    expect($commune->name)->toBe('Adrar')
        ->and($commune->wilaya->name)->toBe('Adrar')
        ->and($commune->daira->name)->toBe('Adrar')
        ->and($commune->wilaya->region->name)->toBe('SUD');
});

it('adds nothing when run again', function () {
    $this->seed(AlgeriaGeoSeeder::class);

    expect(Region::count())->toBe(4)
        ->and(Wilaya::count())->toBe(69)
        ->and(Daira::count())->toBe(551)
        ->and(Commune::count())->toBe(1541);
});

it('never overwrites edited data', function () {
    Region::find(1)->update(['name' => 'My region']);
    Wilaya::find(16)->update(['name' => 'Edited', 'latitude' => 30.5, 'region_id' => 2]);
    Daira::find(101)->update(['name' => 'Edited']);
    Commune::find(101)->update(['name' => 'Edited', 'post_code' => '99999', 'daira_id' => 104]);

    $this->seed(AlgeriaGeoSeeder::class);

    expect(Region::find(1)->name)->toBe('My region')
        ->and(Wilaya::find(16)->name)->toBe('Edited')
        ->and(Wilaya::find(16)->latitude)->toBe(30.5)
        ->and(Wilaya::find(16)->region_id)->toBe(2)
        ->and(Daira::find(101)->name)->toBe('Edited')
        ->and(Commune::find(101)->name)->toBe('Edited')
        ->and(Commune::find(101)->post_code)->toBe('99999')
        ->and(Commune::find(101)->daira_id)->toBe(104);
});

it('keeps rows the app added itself', function () {
    $wilaya = Wilaya::create(['name' => 'My wilaya', 'arabic_name' => 'ولايتي']);

    $this->seed(AlgeriaGeoSeeder::class);

    expect(Wilaya::find($wilaya->id)->name)->toBe('My wilaya')
        ->and(Wilaya::count())->toBe(70);
});

it('adds the new wilayas to an older database', function () {
    Commune::where('wilaya_id', '>', 58)->delete();
    Daira::where('wilaya_id', '>', 58)->delete();
    Wilaya::where('id', '>', 58)->delete();

    expect(Wilaya::count())->toBe(58);

    $this->seed(AlgeriaGeoSeeder::class);

    expect(Wilaya::count())->toBe(69)
        ->and(Commune::where('wilaya_id', 59)->count())->toBeGreaterThan(0)
        ->and(Commune::count())->toBe(1541);
});

it('fills regions and dairas added after the first install', function () {
    DB::table('wilayas')->update(['region_id' => null]);
    DB::table('communes')->update(['daira_id' => null]);

    $this->seed(AlgeriaGeoSeeder::class);

    expect(Wilaya::whereNull('region_id')->count())->toBe(0)
        ->and(Commune::whereNull('daira_id')->count())->toBe(0);
});

it('keeps a link you cleared yourself', function () {
    Wilaya::find(1)->update(['region_id' => null]);
    Commune::find(101)->update(['daira_id' => null]);

    $this->seed(AlgeriaGeoSeeder::class);

    expect(Wilaya::find(1)->region_id)->toBeNull()
        ->and(Commune::find(101)->daira_id)->toBeNull();
});

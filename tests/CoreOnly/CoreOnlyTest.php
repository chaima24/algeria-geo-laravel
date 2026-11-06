<?php

use Chaima24\AlgeriaGeo\Models\Commune;
use Chaima24\AlgeriaGeo\Models\Wilaya;
use Illuminate\Support\Facades\Schema;

it('works with wilayas and communes only', function () {
    expect(Schema::hasTable('regions'))->toBeFalse()
        ->and(Schema::hasTable('dairas'))->toBeFalse()
        ->and(Schema::hasColumn('wilayas', 'region_id'))->toBeFalse()
        ->and(Schema::hasColumn('communes', 'daira_id'))->toBeFalse();

    $wilaya = Wilaya::create(['name' => 'Adrar', 'arabic_name' => 'أدرار']);
    $commune = Commune::create(['wilaya_id' => $wilaya->id, 'name' => 'Tamest', 'arabic_name' => 'تاماست']);

    expect($commune->wilaya->is($wilaya))->toBeTrue()
        ->and($wilaya->communes)->toHaveCount(1);
});

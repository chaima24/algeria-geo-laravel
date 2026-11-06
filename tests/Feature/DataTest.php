<?php

function geoData(string $file): array
{
    return json_decode(file_get_contents(__DIR__."/../../data/{$file}.json"), true, flags: JSON_THROW_ON_ERROR);
}

it('has the expected number of rows', function () {
    expect(geoData('regions'))->toHaveCount(4)
        ->and(geoData('wilayas'))->toHaveCount(69)
        ->and(geoData('dairas'))->toHaveCount(551)
        ->and(geoData('communes'))->toHaveCount(1541);
});

it('has unique ids', function (string $file) {
    $ids = array_column(geoData($file), 'id');

    expect($ids)->toHaveCount(count(array_unique($ids)));
})->with(['regions', 'wilayas', 'dairas', 'communes']);

it('numbers wilayas from 1 to 69', function () {
    expect(array_column(geoData('wilayas'), 'id'))->toBe(range(1, 69));
});

it('links every wilaya to a region', function () {
    $regions = array_column(geoData('regions'), 'id');

    foreach (geoData('wilayas') as $wilaya) {
        expect($regions)->toContain($wilaya['region_id']);
    }
});

it('links every commune to a wilaya and a daira of that wilaya', function () {
    $wilayas = array_column(geoData('wilayas'), 'id');
    $dairas = array_column(geoData('dairas'), 'wilaya_id', 'id');

    foreach (geoData('communes') as $commune) {
        expect($wilayas)->toContain($commune['wilaya_id'])
            ->and($dairas[$commune['daira_id']])->toBe($commune['wilaya_id']);
    }
});

it('gives Algiers 13 groups covering 57 communes', function () {
    $dairas = array_filter(geoData('dairas'), fn (array $daira) => $daira['wilaya_id'] === 16);
    $communes = array_filter(geoData('communes'), fn (array $commune) => $commune['wilaya_id'] === 16);

    expect($dairas)->toHaveCount(13)->and($communes)->toHaveCount(57);
});

it('keeps coordinates inside Algeria with latitude and longitude in the right order', function (string $file) {
    foreach (geoData($file) as $row) {
        expect($row['latitude'])->toBeNumeric()->toBeGreaterThan(18.9)->toBeLessThan(37.3)
            ->and($row['longitude'])->toBeNumeric()->toBeGreaterThan(-8.8)->toBeLessThan(12.1);
    }
})->with(['wilayas', 'communes']);

it('has names in French and Arabic', function (string $file) {
    foreach (geoData($file) as $row) {
        expect($row['name'])->not->toBeEmpty()
            ->and($row['arabic_name'])->not->toBeEmpty();
    }
})->with(['wilayas', 'dairas', 'communes']);

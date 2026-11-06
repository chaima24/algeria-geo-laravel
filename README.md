# Algeria Geo for Laravel

Algerian geography for Laravel, ready to use in your database:

- 69 wilayas, including the 11 created in 2026
- 1,541 communes with French and Arabic names, postal codes and coordinates
- Optional: 4 regions and 551 dairas

## Requirements

- PHP 8.3+
- Laravel 13

Tested on SQLite, MySQL 8 and PostgreSQL 16.

## Installation

```bash
composer require chaima24/algeria-geo-laravel
php artisan algeria-geo:install
```

This publishes the config and migrations, runs `migrate`, and seeds the data. Note that `migrate` also runs any other pending migrations in your app.

Regions and dairas are optional. Add the ones you want:

```bash
php artisan algeria-geo:install --regions --dairas
```

You can add them later by running the command again. Your existing data stays as it is.

In production, add `--force` to run without confirmation.

## Usage

```php
use Chaima24\AlgeriaGeo\Models\Commune;
use Chaima24\AlgeriaGeo\Models\Wilaya;

$wilayas = Wilaya::orderBy('name')->get();

$wilaya = Wilaya::find(16);   // Alger
$wilaya->communes;

$commune = Commune::find(101);
$commune->wilaya;
$commune->name;               // Adrar
$commune->arabic_name;        // أدرار
$commune->post_code;          // 01000
$commune->latitude;
$commune->longitude;
```

With regions and dairas installed:

```php
$wilaya->region;
$wilaya->dairas;
$commune->daira;
$region->wilayas;
$region->communes;
$daira->communes;
```

### Ids

Ids are the official numbers: Adrar is wilaya `1` and Alger is wilaya `16`. Commune and daira ids are the official code without the leading zero, so `0101` is `101`.

To show a wilaya number with its leading zero:

```php
sprintf('%02d', $wilaya->id);   // 01
```

## Tables

| Table | Columns |
|---|---|
| `wilayas` | `id`, `name`, `arabic_name`, `phone_code`, `latitude`, `longitude`, `region_id` (with regions) |
| `communes` | `id`, `wilaya_id`, `name`, `arabic_name`, `post_code`, `latitude`, `longitude`, `daira_id` (with dairas) |
| `regions` | `id`, `name` |
| `dairas` | `id`, `wilaya_id`, `name`, `arabic_name` |

## Extending the models

Extend a package model in your app:

```php
namespace App\Models;

use Chaima24\AlgeriaGeo\Models\Wilaya as BaseWilaya;

class Wilaya extends BaseWilaya
{
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
```

Then set your class in `config/algeria-geo.php`:

```php
'models' => [
    'wilaya' => App\Models\Wilaya::class,
    // ...
],
```

Package relations now return your class, so `$commune->wilaya` is an `App\Models\Wilaya`.

## Seeding

The seeder adds what is missing and never changes rows that already exist, so you can edit your data and run it again safely:

```bash
php artisan db:seed --class="Chaima24\AlgeriaGeo\Database\AlgeriaGeoSeeder"
```

- Your edited rows and the rows your app added stay as they are.
- A row you deleted from the package data comes back on the next run.
- When you add regions or dairas after the first install, the seeder fills `region_id` or `daira_id`. It does this only while that column is empty on every row.

A wilaya can't be deleted while it still has communes or dairas.

### Multi-tenant apps

If your tenant migrations live in their own folder, publish the files and run the seeder from your tenant setup:

```bash
php artisan vendor:publish --tag=algeria-geo-migrations
php artisan vendor:publish --tag=algeria-geo-regions
php artisan vendor:publish --tag=algeria-geo-dairas
```

Move the published migrations to your tenant folder. Regions and dairas are optional, but their migrations must run after the core ones.

## About the data

- **Wilaya coordinates** are those of the wilaya's capital commune. A wilaya is an area, so this is one chosen point, not its exact centre.
- **Commune coordinates** are approximate. They're good for maps, not for exact addresses. Some may be a few kilometres from the real centre.
- **Algiers** has no dairas. Its 13 groups in the `dairas` table are legally *circonscriptions administratives* (decree 26-112 of 2026).
- **Postal codes** are not unique: two communes can share one.
- **Regions** (Centre, Est, Ouest, Sud) are a practical grouping, not an official division. Each of the 11 new wilayas uses the region of the wilaya it was split from.
- **Names** can have more than one spelling. Commune names follow the ONS list where possible. Wilaya 11 uses `Tamanrasset`.

Found a mistake? Please open an issue or a pull request.

## Testing

```bash
composer test
```

## License

The code is released under the [MIT License](LICENSE).

The code and data build on other open-source projects. See [NOTICE](NOTICE) and the [licenses](licenses) folder for credits and their licences.

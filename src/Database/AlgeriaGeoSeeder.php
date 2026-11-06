<?php

namespace Chaima24\AlgeriaGeo\Database;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the rows that are missing and never changes rows that already exist.
 * Regions and dairas are skipped when their tables are not installed.
 */
class AlgeriaGeoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $hasRegions = Schema::hasTable('regions');
            $hasDairas = Schema::hasTable('dairas');

            if ($hasRegions) {
                $this->insertMissing('regions', $this->rows('regions'));
            }

            $wilayas = $this->rows('wilayas');

            if ($hasRegions) {
                $this->fillNewColumn('wilayas', 'region_id', $wilayas);
            }

            $this->insertMissing('wilayas', $this->without($wilayas, $hasRegions ? [] : ['region_id']));

            if ($hasDairas) {
                $this->insertMissing('dairas', $this->rows('dairas'));
            }

            $communes = $this->rows('communes');

            if ($hasDairas) {
                $this->fillNewColumn('communes', 'daira_id', $communes);
            }

            $this->insertMissing('communes', $this->without($communes, $hasDairas ? [] : ['daira_id']));
        });
    }

    protected function rows(string $file): Collection
    {
        $json = file_get_contents(__DIR__."/../../data/{$file}.json");

        return collect(json_decode($json, true, flags: JSON_THROW_ON_ERROR));
    }

    protected function without(Collection $rows, array $columns): Collection
    {
        return $rows->map(fn (array $row) => Arr::except($row, $columns));
    }

    protected function insertMissing(string $table, Collection $rows): void
    {
        $existing = DB::table($table)->pluck('id')->flip();
        $now = now();

        $missing = $rows
            ->reject(fn (array $row) => $existing->has($row['id']))
            ->map(fn (array $row) => $row + ['created_at' => $now, 'updated_at' => $now]);

        $missing->chunk(500)->each(fn (Collection $chunk) => DB::table($table)->insert($chunk->values()->all()));

        if ($missing->isNotEmpty()) {
            $this->syncPostgresSequence($table);
        }

        $this->command?->info("{$table}: {$missing->count()} added");
    }

    /**
     * Fills a link column added after the first install. It runs only while the
     * column is empty on every row, so a value you cleared later stays cleared.
     */
    protected function fillNewColumn(string $table, string $column, Collection $rows): void
    {
        if (DB::table($table)->whereNotNull($column)->exists()) {
            return;
        }

        $rows->groupBy($column)->each(function (Collection $group, int|string $value) use ($table, $column) {
            DB::table($table)->whereIn('id', $group->pluck('id'))->update([$column => $value]);
        });
    }

    /**
     * Rows are inserted with their official ids. PostgreSQL does not move its id
     * counter for those rows, so the app's next new row would clash without this.
     */
    protected function syncPostgresSequence(string $table): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("select setval(pg_get_serial_sequence('{$table}', 'id'), (select max(id) from {$table}))");
        }
    }
}

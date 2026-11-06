<?php

namespace Chaima24\AlgeriaGeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilaya extends Model
{
    protected $table = 'wilayas';

    protected $fillable = ['name', 'arabic_name', 'phone_code', 'latitude', 'longitude', 'region_id'];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(config('algeria-geo.models.region'), 'region_id');
    }

    public function dairas(): HasMany
    {
        return $this->hasMany(config('algeria-geo.models.daira'), 'wilaya_id');
    }

    public function communes(): HasMany
    {
        return $this->hasMany(config('algeria-geo.models.commune'), 'wilaya_id');
    }
}

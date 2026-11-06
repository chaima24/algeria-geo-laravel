<?php

namespace Chaima24\AlgeriaGeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = ['name'];

    public function wilayas(): HasMany
    {
        return $this->hasMany(config('algeria-geo.models.wilaya'), 'region_id');
    }

    public function communes(): HasManyThrough
    {
        return $this->hasManyThrough(
            config('algeria-geo.models.commune'),
            config('algeria-geo.models.wilaya'),
            'region_id',
            'wilaya_id',
        );
    }
}

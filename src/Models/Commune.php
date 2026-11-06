<?php

namespace Chaima24\AlgeriaGeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commune extends Model
{
    protected $table = 'communes';

    protected $fillable = ['wilaya_id', 'daira_id', 'name', 'arabic_name', 'post_code', 'latitude', 'longitude'];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(config('algeria-geo.models.wilaya'), 'wilaya_id');
    }

    public function daira(): BelongsTo
    {
        return $this->belongsTo(config('algeria-geo.models.daira'), 'daira_id');
    }
}

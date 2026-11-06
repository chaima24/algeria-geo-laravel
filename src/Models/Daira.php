<?php

namespace Chaima24\AlgeriaGeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Daira extends Model
{
    protected $table = 'dairas';

    protected $fillable = ['wilaya_id', 'name', 'arabic_name'];

    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(config('algeria-geo.models.wilaya'), 'wilaya_id');
    }

    public function communes(): HasMany
    {
        return $this->hasMany(config('algeria-geo.models.commune'), 'daira_id');
    }
}

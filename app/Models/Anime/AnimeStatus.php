<?php

namespace App\Models\Anime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimeStatus extends Model
{
    protected $table = 'statuses';

    protected $fillable = [
        'name',
        'name_eng',
    ];

    public function animes(): HasMany
    {
        return $this->hasMany(Anime::class, 'status_id');
    }
}

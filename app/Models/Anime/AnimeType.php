<?php

namespace App\Models\Anime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimeType extends Model
{
    protected $table = 'types';

    protected $fillable = [
        'name',
        'name_eng',
    ];

    public function animes(): HasMany
    {
        return $this->hasMany(Anime::class, 'type_id');
    }
}

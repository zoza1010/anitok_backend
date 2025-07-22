<?php

namespace App\Models\Anime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimeAgeRating extends Model
{
    protected $table = 'age_ratings';

    protected $fillable = [
        'name',
        'name_eng',
    ];

    public function animes(): HasMany
    {
        return $this->hasMany(Anime::class, 'age_rating_id');
    }
}

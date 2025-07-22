<?php

namespace App\Models\Anime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Anime extends Model
{
    protected $fillable = [
        'title',
        'title_eng',
        'description',
        'poster_url',
        'aired_on',
        'released_on',
        'next_episode_at',
        'duration',
        'episodes',
        'episodes_aired',
        'age_rating_id',
        'status_id',
        'type_id',
    ];

    public function ageRating(): BelongsTo
    {
        return $this->belongsTo(AnimeAgeRating::class, 'age_rating_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AnimeStatus::class, 'status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AnimeType::class, 'type_id');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'anime_genre');
    }
}

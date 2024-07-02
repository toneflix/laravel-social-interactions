<?php

namespace ToneflixCode\SocialInteractions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ToneflixCode\SocialInteractions\Models\Casts\Reaction;

final class SocialInteraction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'votes',
        'saved',
        'liked',
        'disliked',
        'reaction',
        'interactor_id',
        'interactor_type',
        'interactable_id',
        'interactable_type',
    ];

    /**
     * The model's attributes.
     *
     * @var array<string, string|int|bool>
     */
    protected $attributes = [
        'votes' => 0,
        'saved' => false,
        'liked' => false,
        'disliked' => false,
        'reaction' => 0,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'votes' => 'integer',
        'saved' => 'boolean',
        'liked' => 'boolean',
        'disliked' => 'boolean',
        'reaction' => Reaction::class,
    ];

    /**
     * Get the table associated with the model
     */
    public function getTable(): string
    {
        return config('social-interactions.tables.interactions', 'social_interactions');
    }

    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }
}

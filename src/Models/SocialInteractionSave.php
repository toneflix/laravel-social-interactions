<?php

namespace ToneflixCode\SocialInteractions\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class SocialInteractionSave extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'interactor_type',
        'interactor_id',
        'list_name',
        'public',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'list_name' => 'default',
        'public' => false,
    ];

    /**
     * Get the table associated with the model
     */
    public function getTable(): string
    {
        return config('social-interactions.tables.saves', 'social_interaction_saves');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'public' => 'boolean',
        ];
    }

    public function saveable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeList(Builder $query, $list = 'default'): void
    {
        $query->whereListName($list);
    }
}

<?php

namespace ToneflixCode\SocialInteractions\Models;

use Illuminate\Database\Eloquent\Model;

final class SocialInteractionSave extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
}

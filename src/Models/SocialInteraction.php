<?php

namespace ToneflixCode\SocialInteractions\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
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

    public function savedItem(): HasOne
    {
        return $this->hasOne(SocialInteractionSave::class, 'interaction_id');
    }

    /**
     * Get all the interaction data for this model
     */
    public function interactionData(): Attribute
    {
        return Attribute::make(get: function () {
            $map = [
                'vote' => str(config('social-interactions.key_maps.vote', 'vote'))->toString(),
                'like' => str(config('social-interactions.key_maps.like', 'like'))->toString(),
                'dislike' => str(config('social-interactions.key_maps.dislike', 'dislike'))->toString(),
                'reaction' => str(config('social-interactions.key_maps.reaction', 'reaction'))->toString(),

                'votes' => str(config('social-interactions.key_maps.vote', 'vote'))->plural(2)->toString(),
                'likes' => str(config('social-interactions.key_maps.like', 'like'))->plural(2)->toString(),
                'dislikes' => str(config('social-interactions.key_maps.dislike', 'dislike'))->plural(2)->toString(),
                'reactions' => str(config('social-interactions.key_maps.reaction', 'reaction'))->plural(2)->toString(),

                'saved' => str(config('social-interactions.key_maps.save', 'save'))->pastTense()->toString(),
                'voted' => str(config('social-interactions.key_maps.vote', 'vote'))->pastTense()->toString(),
                'liked' => str(config('social-interactions.key_maps.like', 'like'))->pastTense()->toString(),
                'reacted' => str(config('social-interactions.key_maps.react', 'react'))->pastTense()->toString(),
                'disliked' => str(config('social-interactions.key_maps.dislike', 'dislike'))->pastTense()->toString(),
            ];

            $data = new Collection([
                $map['votes'] => (int) $this->interactable->socialInteractions()->sum('votes'),
                $map['likes'] => (int) $this->interactable->socialInteractions()->whereLiked(true)->count(),
                $map['dislikes'] => (int) $this->interactable->socialInteractions()->whereDisliked(true)->count(),
                $map['reactions'] => (int) $this->interactable->socialInteractions()->whereNotNull('reaction')->count(),
            ]);

            $icons = config('social-interactions.icon_classes');
            $colors = config('social-interactions.reaction_colors');

            $reaction_icon = $icons['like'][0];
            $reaction_color = $colors['like'];

            if ($this->reaction) {
                $reaction_icon = collect($icons)->first(fn ($i, $k) => $k === $this->reaction);
                $reaction_color = collect($colors)->first(fn ($i, $k) => $k === $this->reaction);
            } elseif ($this->liked) {
                $reaction_icon = $icons['like'][0] ?? '';
                $reaction_color = $colors['like'][0] ?? '';
            }

            $data = $data->merge([
                $map['saved'] => $this->saved,
                'list_name' => $this->savedItem->list_name ?? null,
                $map['voted'] => $this->votes > 0,
                $map['liked'] => $this->liked,
                $map['reacted'] => $this->liked || $this->reaction,
                "own{$map['votes']}" => $this->votes,
                $map['disliked'] => $this->disliked,
                $map['reaction'] => $this->reaction,
                "{$map['reaction']}_color" => @is_array($reaction_color) ? $reaction_color[0] : $reaction_color,
                'state_icons' => [
                    $map['saved'] => @$this->saved ? $icons['save'][0] : $icons['save'][1],
                    $map['voted'] => @$this->saved ? $icons['vote'][0] : $icons['vote'][1],
                    $map['disliked'] => @$this->saved ? $icons['dislike'][0] : $icons['dislike'][1],
                    $map['reaction'] => @is_array($reaction_icon) ? $reaction_icon[0] : $reaction_icon,
                ],
            ]);

            return $data;
        });
    }
}

<?php

namespace ToneflixCode\SocialInteractions\Traits;

use Illuminate\Database\Eloquent\Builder;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use ToneflixCode\SocialInteractions\Events\SocialInteractionDone;
use ToneflixCode\SocialInteractions\Exception\InvalidInteractionException;
use ToneflixCode\SocialInteractions\Models\SocialInteractionSave;

/**
 * @mixin Model
 */
trait HasSocialInteractions
{
    public function socialInteractions(): MorphMany
    {
        return $this->morphMany(SocialInteraction::class, 'interactable');
    }

    public function socialInteractionSaves(): MorphMany
    {
        return $this->morphMany(SocialInteractionSave::class, 'saveable');
    }

    /**
     * Get all the interaction data for this model
     *
     * @param Model|CanSocialInteract|null $interactor
     * @return Collection
     */
    public function socialInteractionData(Model|CanSocialInteract $interactor = null): Collection
    {
        $data = new Collection([
            'votes' => $this->socialInteractions()->sum('votes'),
            'likes' => $this->socialInteractions()->whereLiked(true)->count(),
            'dislikes' => $this->socialInteractions()->whereDisliked(true)->count(),
            'reactions' => $this->socialInteractions()->whereNotNull('reactions')->count(),
        ]);

        if ($interactor) {
            $interaction = $this->modelInteraction($interactor);

            $icons = config('social-interactions.icon_classes');
            $colors = config('social-interactions.reaction_colors');

            $reaction_icon = $icons['like'][0];
            $reaction_color = $colors['like'];

            if ($interaction->reaction) {
                $reaction_icon = collect($icons)->first(fn ($i, $k) => $k === $interaction->reaction);
                $reaction_color = collect($colors)->first(fn ($i, $k) => $k === $interaction->reaction);
            } elseif ($interaction->liked) {
                $reaction_icon = $icons['like'][0] ?? '';
                $reaction_color = $colors['like'][0] ?? '';
            }

            $data = $data->merge([
                'saved' => $interaction->saved,
                'voted' => $interaction->votes > 0,
                'liked' => $interaction->liked,
                'reacted' => $interaction->liked || $interaction->reaction,
                'ownvotes' => $interaction->votes,
                'disliked' => $interaction->disliked,
                'reaction' => $interaction->reaction,
                'reaction_color' => @is_array($reaction_color) ? $reaction_color[0] : $reaction_color,
                'state_icons' => [
                    'saved' => @$interaction->saved ? $icons['save'][0] : $icons['save'][1],
                    'voted' => @$interaction->saved ? $icons['vote'][0] : $icons['vote'][1],
                    'disliked' => @$interaction->saved ? $icons['dislike'][0] : $icons['dislike'][1],
                    'reaction' => @is_array($reaction_icon) ? $reaction_icon[0] : $reaction_icon,
                ],
            ]);
        }

        return $data;
    }

    /**
     * Leave a reaction on the model
     *
     * @param Model|CanSocialInteract $interactor
     * @param int|string|bool $reaction
     * @return SocialInteraction
     */
    public function react(Model|CanSocialInteract $interactor, int|string|bool $reaction): SocialInteraction
    {
        $a_list = array_keys(array_merge(config('social-interactions.available_reactions'), ['dislike']));
        $allowed = in_array($reaction, $a_list);
        $reactions = array_merge(config('social-interactions.available_reactions'), ['dislike']);

        if (!is_bool($reaction) && !$allowed && !in_array($reaction, $reactions)) {
            throw InvalidInteractionException::invalidReaction();
        }

        if (!config('social-interactions.enable_dislikes', false) && $reaction === 'dislike') {
            throw InvalidInteractionException::dislikeDisabled();
        }

        if (
            in_array($reaction, array_merge(array_slice($a_list, 2), config('social-interactions.available_reactions', [])), true) &&
            !config('social-interactions.enable_reactions', false) &&
            $reaction !== 'like'
        ) {
            throw InvalidInteractionException::reactionsDisabled();
        }

        $interaction = $this->socialInteractions()->firstOrCreate([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ]);

        $liked = in_array($reaction, ['like', 1, true], true) ? (!$interaction?->liked) : false;
        $interaction->liked = $reaction === 'dislike' ? false : $liked;
        $interaction->disliked = $reaction === 'dislike' ? !$interaction?->disliked : false;
        $interaction->reaction = $reaction;
        $interaction->save();

        $set = is_bool($reaction)
            ? 'liked'
            : (config('social-interactions.enable_reactions', false)
                ? 'reaction'
                : (['', 'liked'][$reaction] ?? $reaction . 'd')
            );

        SocialInteractionDone::dispatch($interaction, $set);

        return $interaction;
    }

    /**
     * Save a model
     *
     * @param Model|CanSocialInteract $interactor
     * @param bool $save
     * @return SocialInteraction
     */
    public function toggleSave(Model|CanSocialInteract $interactor, bool $save = true): SocialInteraction
    {
        $interaction = $this->socialInteractions()->updateOrCreate([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ], [
            'saved' => $save,
        ]);

        SocialInteractionDone::dispatch($interaction, 'saved');

        return $interaction;
    }

    /**
     * Save a model to list
     *
     * @param Model|CanSocialInteract $interactor
     * @param bool $save
     * @param string $list_name
     * @param bool $public
     * @return SocialInteractionSave
     */
    public function toggleSaveToList(
        Model|CanSocialInteract $interactor,
        bool $save = true,
        string $list_name = 'default',
        bool $public = false,
    ): SocialInteractionSave {
        if (!config('social-interactions.enable_save_lists', false)) {
            throw InvalidInteractionException::saveListDisabled();
        }

        $interaction = $this->socialInteractionSaves()->firstOrCreate([
            'list_name' => $list_name,
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ]);

        if ($save === false) {
            $interaction->delete();
        } else {
            $interaction->public = $public;
            $interaction->save();
        }

        SocialInteractionDone::dispatch($interaction, 'save_list');

        return $interaction;
    }

    /**
     * Vote for a model
     *
     * @param Model|CanSocialInteract $interactor
     * @param bool $vote
     * @return SocialInteraction
     */
    public function giveVote(Model|CanSocialInteract $interactor, bool $vote = true): SocialInteraction
    {
        $interaction = $this->socialInteractions()->firstOrCreate([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ]);

        if ($vote === true && ($interaction->votes < 1 || config('social-interactions.multiple_votes', false))) {
            $interaction->increment('votes');
        } else if (config('social-interactions.enable_unvote', false)) {
            $interaction->votes = 0;
            $interaction->save();
        }

        SocialInteractionDone::dispatch($interaction, 'votes');

        return $interaction;
    }

    /**
     * Get the reaction for the specified reactor
     *
     * @param Model|CanSocialInteract $interactor
     * @return SocialInteraction
     */
    public function modelInteraction(Model|CanSocialInteract $interactor): SocialInteraction
    {
        return $this->socialInteractions()
            ->whereInteractorType($interactor->getMorphClass())
            ->whereInteractorId($interactor->id)
            ->firstOrNew();
    }

    /**
     * Check if a model has been saved
     *
     * @param Model|CanSocialInteract $interactor
     * @param string|null $list
     * @return bool
     */
    public function isSaved(Model|CanSocialInteract $interactor, ?string $list = null): bool
    {
        return self::filterSaved($interactor, $list)->exists();
    }

    /**
     * Scope to return only saved models
     *
     * @param Builder $query
     * @param Model|CanSocialInteract $interactor
     * @param string|null $list
     * @return void
     */
    public function scopeFilterSaved(Builder $query, Model|CanSocialInteract $interactor, ?string $list = null): void
    {
        $query->whereHas(
            'socialInteractions',
            fn ($q) => $q->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
                ->whereSaved(true)
        )->orWhereHas(
            'socialInteractionSaves',
            fn (Builder $q) => $q->when($list, fn (Builder $q) => $q->whereListName($list))
                ->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
        );
    }

    /**
     * Check if a model has been voted for
     *
     * @param Model|CanSocialInteract $interactor
     * @return bool
     */
    public function isVoted(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isVoted($interactor)->exists();
    }

    /**
     * Scope to return only voted models
     *
     * @param Builder $query
     * @param Model|CanSocialInteract $interactor
     * @return void
     */
    public function scopeIsVoted(Builder $query, Model|CanSocialInteract $interactor): void
    {
        $query->whereHas(
            'socialInteractions',
            fn ($q) => $q->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
                ->where('votes', '>', 0)
        );
    }

    /**
     * Check if a model has been reacted to
     *
     * @param Model|CanSocialInteract $interactor
     * @return bool
     */
    public function isReacted(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isReacted($interactor)->exists();
    }

    /**
     * Scope to return only models reacted to
     *
     * @param Builder $query
     * @param Model|CanSocialInteract $interactor
     * @return void
     */
    public function scopeIsReacted(Builder $query, Model|CanSocialInteract $interactor): void
    {
        $query->whereHas(
            'socialInteractions',
            fn (Builder $q) => $q->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
                ->where(function (Builder $q) {
                    $q->whereLiked(true);
                    $q->orWhereNot('reaction', '=');
                })
        );
    }

    /**
     * Check if a model has been liked
     *
     * @param Model|CanSocialInteract $interactor
     * @return bool
     */
    public function isLiked(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isLiked($interactor)->exists();
    }

    /**
     * Scope to return only liked models
     *
     * @param Builder $query
     * @param Model|CanSocialInteract $interactor
     * @return void
     */
    public function scopeIsLiked(Builder $query, Model|CanSocialInteract $interactor): void
    {
        $query->whereHas(
            'socialInteractions',
            fn (Builder $q) => $q->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
                ->where(function (Builder $q) {
                    $q->whereLiked(true);
                })
        );
    }

    /**
     * Check if a model has been disliked
     *
     * @param Model|CanSocialInteract $interactor
     * @return bool
     */
    public function isDisliked(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isDisliked($interactor)->exists();
    }

    /**
     * Scope to return only liked models
     *
     * @param Builder $query
     * @param Model|CanSocialInteract $interactor
     * @return void
     */
    public function scopeIsDisliked(Builder $query, Model|CanSocialInteract $interactor): void
    {
        $query->whereHas(
            'socialInteractions',
            fn (Builder $q) => $q->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
                ->where(function (Builder $q) {
                    $q->whereDisliked(true);
                })
        );
    }
}

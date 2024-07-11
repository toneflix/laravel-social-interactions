<?php

namespace ToneflixCode\SocialInteractions\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use ToneflixCode\SocialInteractions\Events\SocialInteractionDone;
use ToneflixCode\SocialInteractions\Exception\InvalidInteractionException;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
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
     */
    public function socialInteractionData(Model|CanSocialInteract|null $interactor = null): Collection
    {
        $data = new Collection();

        if ($interactor) {
            $interaction = $this->modelInteraction($interactor)->interactionData;

            $data = $data->merge($interaction);
        } else {
            $map = [
                'votes' => str(config('social-interactions.key_maps.vote', 'vote'))->plural(2)->toString(),
                'likes' => str(config('social-interactions.key_maps.like', 'like'))->plural(2)->toString(),
                'dislikes' => str(config('social-interactions.key_maps.dislike', 'dislike'))->plural(2)->toString(),
                'reactions' => str(config('social-interactions.key_maps.reaction', 'reaction'))->plural(2)->toString(),
            ];

            $data = new Collection([
                $map['votes'] => (int) $this->socialInteractions()->sum('votes'),
                $map['likes'] => (int) $this->socialInteractions()->whereLiked(true)->count(),
                $map['dislikes'] => (int) $this->socialInteractions()->whereDisliked(true)->count(),
                $map['reactions'] => (int) $this->socialInteractions()->whereNotNull('reaction')->count(),
            ]);
        }

        return $data;
    }

    /**
     * Leave a reaction on the model
     */
    public function react(Model|CanSocialInteract $interactor, int|string|bool $reaction): SocialInteraction
    {
        $a_list = array_keys(array_merge(config('social-interactions.available_reactions'), ['dislike']));
        $allowed = in_array($reaction, $a_list);
        $reactions = array_merge(config('social-interactions.available_reactions'), ['dislike']);

        if (! is_bool($reaction) && ! $allowed && ! in_array($reaction, $reactions)) {
            throw InvalidInteractionException::invalidReaction();
        }

        if (! config('social-interactions.enable_dislikes', false) && $reaction === 'dislike') {
            throw InvalidInteractionException::dislikeDisabled();
        }

        if (
            in_array($reaction, array_merge(array_slice($a_list, 2), config('social-interactions.available_reactions', [])), true) &&
            ! config('social-interactions.enable_reactions', false) &&
            $reaction !== 'like'
        ) {
            throw InvalidInteractionException::reactionsDisabled();
        }

        $interaction = $this->socialInteractions()->firstOrCreate([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ]);

        $liked = in_array($reaction, ['like', 1, true], true) ? (! $interaction?->liked) : false;
        $interaction->liked = $reaction === 'dislike' ? false : $liked;
        $interaction->disliked = $reaction === 'dislike' ? ! $interaction?->disliked : false;
        $interaction->reaction = $reaction;
        $interaction->save();

        $set = is_bool($reaction)
            ? 'liked'
            : (config('social-interactions.enable_reactions', false)
                ? 'reaction'
                : (['', 'liked'][$reaction] ?? $reaction.'d')
            );

        SocialInteractionDone::dispatch($interaction, $set);

        return $interaction;
    }

    /**
     * Save a model
     */
    public function toggleSave(Model|CanSocialInteract $interactor, bool $save = true, bool $skipEvent = false): SocialInteraction
    {
        $interaction = $this->socialInteractions()->updateOrCreate([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ], [
            'saved' => $save,
        ]);

        if (! $skipEvent) {
            SocialInteractionDone::dispatch($interaction, 'saved');
        }

        return $interaction;
    }

    /**
     * Save a model to list
     */
    public function toggleSaveToList(
        Model|CanSocialInteract $interactor,
        bool $save = true,
        string $list_name = 'default',
        bool $public = false,
    ): SocialInteraction {
        if (! config('social-interactions.enable_save_lists', false)) {
            throw InvalidInteractionException::saveListDisabled();
        }

        $list = $this->toggleSave($interactor, $save, true)->savedItem()->firstOrCreate([
            'list_name' => $list_name,
            'saveable_id' => $this->id,
            'saveable_type' => $this->getMorphClass(),
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ]);

        if ($save === false) {
            $list->delete();
        } else {
            $list->public = $public;
            $list->save();
        }

        SocialInteractionDone::dispatch($list, 'save_list');

        return $list->interaction;
    }

    /**
     * Vote for a model
     */
    public function giveVote(Model|CanSocialInteract $interactor, bool $vote = true): SocialInteraction
    {
        $interaction = $this->socialInteractions()->firstOrCreate([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ]);

        if ($vote === true && ($interaction->votes < 1 || config('social-interactions.multiple_votes', false))) {
            $interaction->increment('votes');
        } elseif (config('social-interactions.enable_unvote', false)) {
            $interaction->votes = 0;
            $interaction->save();
        }

        SocialInteractionDone::dispatch($interaction, 'votes');

        return $interaction;
    }

    /**
     * Get the reaction for the specified reactor
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
     */
    public function isSaved(Model|CanSocialInteract $interactor, ?string $list = null): bool
    {
        return self::filterSaved($interactor, $list)->exists();
    }

    /**
     * Scope to return only saved models
     */
    public function scopeFilterSaved(Builder $query, Model|CanSocialInteract $interactor, ?string $list = null): void
    {
        $query->whereHas(
            'socialInteractions',
            function ($qx) use ($interactor, $list) {
                $qx->where(function ($q) use ($interactor) {
                    $q->whereInteractorType($interactor->getMorphClass())
                        ->whereInteractorId($interactor->id)
                        ->whereSaved(true);
                })->orWhereHas(
                    'savedItem',
                    fn (Builder $q) => $q->when($list, fn (Builder $q) => $q->whereListName($list))
                        ->whereInteractorType($interactor->getMorphClass())
                        ->whereInteractorId($interactor->id)
                );
            }
        );
    }

    /**
     * Check if a model has been voted for
     */
    public function isVoted(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isVoted($interactor)->exists();
    }

    /**
     * Scope to return only voted models
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
     */
    public function isReacted(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isReacted($interactor)->exists();
    }

    /**
     * Scope to return only models reacted to
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
     */
    public function isLiked(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isLiked($interactor)->exists();
    }

    /**
     * Scope to return only liked models
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
     */
    public function isDisliked(Model|CanSocialInteract $interactor): bool
    {
        return $this->query()->isDisliked($interactor)->exists();
    }

    /**
     * Scope to return only liked models
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

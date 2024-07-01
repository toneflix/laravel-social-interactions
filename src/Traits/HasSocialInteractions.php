<?php

namespace ToneflixCode\SocialInteractions\Traits;

use Illuminate\Database\Eloquent\Builder;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use ToneflixCode\SocialInteractions\Events\SocialInteractionDone;
use ToneflixCode\SocialInteractions\Exception\InvalidInteractionException;

/**
 * @property Collection<TKeys,Collection> $approvableNotifier Get the entity's notifier.
 * @mixin Model
 */
trait HasSocialInteractions
{
    public function socialInteractions(): MorphMany
    {
        return $this->morphMany(SocialInteraction::class, 'interactable');
    }

    /**
     * Leave a reaction on the model
     *
     * @param CanSocialInteract $interactor
     * @param int|string|bool $reaction
     * @return SocialInteraction
     */
    public function react(Model $interactor, int|string|bool $reaction): SocialInteraction
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

        unset($a_list[0], $a_list[1]);

        if (
            in_array($reaction, array_merge($a_list, config('social-interactions.available_reactions', [])), true) &&
            !config('social-interactions.enable_reactions', false) &&
            $reaction !== 'like'
        ) {
            throw InvalidInteractionException::reactionsDisabled();
        }

        $interaction = $this->socialInteractions()->firstWhere([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ]);

        $interaction = $this->socialInteractions()->updateOrCreate([
            'interactor_id' => $interactor->id,
            'interactor_type' => $interactor->getMorphClass(),
        ], [
            'liked' => $reaction === 'like' ? (!$interaction?->liked) : $reaction,
            'disliked' => $reaction === 'dislike' ? !$interaction?->disliked : false,
            'reaction' => $reaction,
        ]);

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
     * Leave a reaction on the model
     *
     * @param CanSocialInteract $interactor
     * @param integer|string $reaction
     * @return SocialInteraction
     */
    public function toggleSave(Model $interactor, bool $save = true): SocialInteraction
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
     * Leave a reaction on the model
     *
     * @param CanSocialInteract $interactor
     * @param integer|string $reaction
     * @return SocialInteraction
     */
    public function giveVote(Model $interactor, bool $vote = true): SocialInteraction
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

    public function isSaved(Model $interactor)
    {
        return $this->query()->isSaved($interactor)->exists();
    }

    public function scopeIsSaved(Builder $query, ?Model $interactor): void
    {
        $query->whereHas(
            'socialInteractions',
            fn ($q) => $q->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
                ->whereSaved(true)
        );
    }

    public function isVoted(Model $interactor)
    {
        return $this->query()->isVoted($interactor)->exists();
    }

    public function scopeIsVoted(Builder $query, ?Model $interactor): void
    {
        $query->whereHas(
            'socialInteractions',
            fn ($q) => $q->whereInteractorType($interactor->getMorphClass())
                ->whereInteractorId($interactor->id)
                ->where('votes', '>', 0)
        );
    }
}

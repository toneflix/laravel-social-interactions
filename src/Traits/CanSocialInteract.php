<?php

namespace ToneflixCode\SocialInteractions\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use ToneflixCode\SocialInteractions\Exception\InvalidInteractionException;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use ToneflixCode\SocialInteractions\Models\SocialInteractionSave;

/**
 * @property Collection $saved_social_lists Get the names of all social lists created
 */
trait CanSocialInteract
{
    public function socialInteracts(): MorphMany
    {
        return $this->morphMany(SocialInteraction::class, 'interactor');
    }

    public function savedSocialInteracts(): MorphMany
    {
        return $this->morphMany(SocialInteractionSave::class, 'interactor');
    }

    /**
     * Get the names of all social lists created
     */
    public function savedSocialLists(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->savedSocialInteracts()->groupBy('list_name')->pluck('list_name')
        );
    }

    /**
     * Delete a particular saved list or all list
     */
    public function deleteSavedSocialList(string|bool $name): Collection
    {
        if ($name === true) {
            $this->savedSocialInteracts()->delete();
        } else {
            $list = $this->savedSocialInteracts()->whereListName($name)->first();
            if ($list) {
                $list->interaction->saved = false;
                $list->interaction->save();
            }
            $this->savedSocialInteracts()->whereListName($name)->delete();
        }

        return $this->saved_social_lists;
    }

    /**
     * Leave a reaction on a model
     *
     * @param  int|string  $reaction
     */
    public function leaveReaction(Model|HasSocialInteractions $interactable, int|string|bool $reaction): SocialInteraction
    {
        if (!method_exists($interactable, 'react')) {
            throw InvalidInteractionException::message($interactable);
        }

        return $interactable->react($this, $reaction);
    }
}
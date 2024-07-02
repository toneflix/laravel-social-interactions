<?php

namespace ToneflixCode\SocialInteractions\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use ToneflixCode\SocialInteractions\Exception\InvalidInteractionException;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use ToneflixCode\SocialInteractions\Models\SocialInteractionSave;

use function Pest\Laravel\delete;

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
     * Undocumented function
     *
     * @param HasSocialInteractions $interactable
     * @param integer|string $reaction
     * @return SocialInteraction
     */
    public function leaveReaction(Model $interactable, int|string|bool $reaction): SocialInteraction
    {
        if (!method_exists($interactable, 'react')) {
            throw InvalidInteractionException::message($interactable);
        }

        return $interactable->react($this, $reaction);
    }
}
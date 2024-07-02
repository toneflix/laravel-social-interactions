<?php

namespace ToneflixCode\SocialInteractions\Exception;

use Illuminate\Database\Eloquent\Model;
use ToneflixCode\SocialInteractions\Traits\HasSocialInteractions;

/**
 * Exception thrown when attempting to interact with a model that does not
 * use the HasSocialInteractions trait.
 */
final class InvalidInteractionException extends \Exception
{
    /**
     * @param  Model|HasSocialInteractions  $model
     */
    public static function message(Model $model): self
    {
        return new self(
            $model->getMorphClass().' is not using the ToneflixCode\SocialInteractions\Traits\HasSocialInteractions trait'
        );
    }

    public static function invalidReaction(): self
    {
        if (config('social-interactions.enable_reactions')) {
            return new self(
                implode('', [
                    'Invalid reaction, supported reactions include: ',
                    collect(config('social-interactions.available_reactions'))->map(fn ($r, $i) => ($i + 1).": $r")->join(', '),
                ])
            );
        }

        return new self('Invalid Reaction: Send "true" to like or "false" to unlike.');
    }

    public static function dislikeDisabled(): self
    {
        return new self('Dislike are disabled.');
    }

    public static function saveListDisabled(): self
    {
        return new self('Saving to lists is disabled.');
    }

    public static function reactionsDisabled(): self
    {
        return new self('Reactions are disabled.');
    }
}

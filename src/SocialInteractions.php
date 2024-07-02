<?php

namespace ToneflixCode\SocialInteractions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use ToneflixCode\SocialInteractions\Models\SocialInteractionSave;
use ToneflixCode\SocialInteractions\Traits\CanSocialInteract;
use ToneflixCode\SocialInteractions\Traits\HasSocialInteractions;

/**
 * @method SocialInteraction leaveReaction(Model|CanSocialInteract $interactor, Model $interactable, int|string|bool $reaction) Leave a reaction on a model
 * @method MorphMany socialInteracts(Model|CanSocialInteract $interactor)
 * @method MorphMany savedSocialInteracts(Model|CanSocialInteract $interactor)
 * @method MorphMany socialInteractions(Model|HasSocialInteractions $interactable)
 * @method MorphMany socialInteractionSaves(Model|HasSocialInteractions $interactable)
 * @method Collection socialInteractionData(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor = null) Get all the interaction data for this model
 * @method SocialInteraction react(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor = null, int|string|bool $reaction) Leave a reaction on the model
 * @method SocialInteraction toggleSave(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, bool $save = true) Save a model
 * @method SocialInteractionSave toggleSaveToList(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, bool $save = true, string $list_name = 'default', bool $public = false) Save a model to list
 * @method Collection deleteSavedSocialList(Model|CanSocialInteract $interactor, string|bool $name) Delete a particular saved list or all list
 * @method SocialInteraction giveVote(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, bool $vote = true) Vote for a model
 * @method SocialInteraction modelInteraction(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Get the reaction for the specified reactor
 * @method bool isSaved(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, ?string $list = null) Check if a model has been saved
 * @method bool isVoted(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been voted for
 * @method bool isReacted(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been reacted to
 * @method bool isLiked(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been been liked
 * @method bool isDisliked(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been been disliked
 * @method bool isDisliked(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been been disliked
 */
class SocialInteractions
{
    /**
     * Call methods on the first dynamically caught parameter
     *
     * @return void
     */
    public function __call(string $name, array $params)
    {
        return $params[0]->{$name}(...array_slice($params, 1));
    }
}

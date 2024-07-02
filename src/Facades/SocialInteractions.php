<?php

namespace ToneflixCode\SocialInteractions\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use ToneflixCode\SocialInteractions\Models\SocialInteractionSave;
use ToneflixCode\SocialInteractions\Traits\CanSocialInteract;
use ToneflixCode\SocialInteractions\Traits\HasSocialInteractions;

/**
 * @method static SocialInteraction leaveReaction(Model|CanSocialInteract $interactor, Model $interactable, int|string|bool $reaction) Leave a reaction on a model
 * @method static MorphMany socialInteracts(Model|CanSocialInteract $interactor)
 * @method static MorphMany savedSocialInteracts(Model|CanSocialInteract $interactor)
 * @method static MorphMany socialInteractions(Model|HasSocialInteractions $interactable)
 * @method static MorphMany socialInteractionSaves(Model|HasSocialInteractions $interactable)
 * @method static Collection socialInteractionData(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor = null) Get all the interaction data for this model
 * @method static SocialInteraction react(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor = null, int|string|bool $reaction) Leave a reaction on the model
 * @method static SocialInteraction toggleSave(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, bool $save = true) Save a model
 * @method static SocialInteractionSave toggleSaveToList(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, bool $save = true, string $list_name = 'default', bool $public = false) Save a model to list
 * @method static Collection deleteSavedSocialList(Model|CanSocialInteract $interactor, string|bool $name) Delete a particular saved list or all list
 * @method static SocialInteraction giveVote(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, bool $vote = true) Vote for a model
 * @method static SocialInteraction modelInteraction(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Get the reaction for the specified reactor
 * @method static bool isSaved(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor, ?string $list = null) Check if a model has been saved
 * @method static bool isVoted(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been voted for
 * @method static bool isReacted(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been reacted to
 * @method static bool isLiked(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been been liked
 * @method static bool isDisliked(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been been disliked
 * @method static bool isDisliked(Model|HasSocialInteractions $interactable, Model|CanSocialInteract $interactor) Check if a model has been been disliked
 */
class SocialInteractions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'social-interactions';
    }
}

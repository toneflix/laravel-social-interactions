<?php

use ToneflixCode\SocialInteractions\Exception\InvalidInteractionException;
use ToneflixCode\SocialInteractions\Tests\Models\Post;

it('throws exception when attempting to interact with an invalid model', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    config(['social-interactions.enable_reactions' => false]);

    $user->leaveReaction($user, true);
})->throws(InvalidInteractionException::class);

it('throws exception if saving to list when saving to list is disabled', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $post->toggleSaveToList($user, true, 'default');
})->throws(InvalidInteractionException::class, 'Saving to lists is disabled.');

it('throws exception if disliking when dislikes are disabled', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $user->leaveReaction(Post::factory()->create(), 'dislike');
})->throws(InvalidInteractionException::class, 'Dislike are disabled.');

it('throws exception if reacting when reactions are disabled', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $user->leaveReaction(Post::factory()->create(), 'love');
})->throws(InvalidInteractionException::class, "Reactions are disabled.");
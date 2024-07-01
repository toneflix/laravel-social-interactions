<?php

use ToneflixCode\SocialInteractions\Exception\InvalidInteractionException;

it('throws exception when attemptiing to interact with an invalid model', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    config(['social-interactions.enable_reactions' => false]);

    $user->leaveReaction($user, true);
})->throws(InvalidInteractionException::class);

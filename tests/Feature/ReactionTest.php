<?php

use ToneflixCode\SocialInteractions\Tests\Models\Post;

test('can react "like" to item', function () {

    config(['social-interactions.enable_reactions' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 1);
    $r2 = $user->leaveReaction(Post::factory()->create(), 'like');

    expect($r1->reaction === 'like')->toBeTrue();
    expect($r2->reaction === 'like')->toBeTrue();
});

test('can react "love" to item', function () {
    config(['social-interactions.enable_reactions' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 2);
    $r2 = $user->leaveReaction(Post::factory()->create(), 'love');

    expect($r1->reaction === 'love')->toBeTrue();
    expect($r2->reaction === 'love')->toBeTrue();
});

test('can react "haha" to item', function () {

    config(['social-interactions.enable_reactions' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 3);
    $r2 = $user->leaveReaction(Post::factory()->create(), 'haha');

    expect($r1->reaction === 'haha')->toBeTrue();
    expect($r2->reaction === 'haha')->toBeTrue();
});

test('can react "wow" to item', function () {

    config(['social-interactions.enable_reactions' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 4);
    $r2 = $user->leaveReaction(Post::factory()->create(), 'wow');

    expect($r1->reaction === 'wow')->toBeTrue();
    expect($r2->reaction === 'wow')->toBeTrue();
});

test('can react "sad" to item', function () {

    config(['social-interactions.enable_reactions' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 5);
    $r2 = $user->leaveReaction(Post::factory()->create(), 'sad');

    expect($r1->reaction === 'sad')->toBeTrue();
    expect($r2->reaction === 'sad')->toBeTrue();
});

test('can react "angry" to item', function () {

    config(['social-interactions.enable_reactions' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 6);
    $r2 = $user->leaveReaction(Post::factory()->create(), 'angry');

    expect($r1->reaction === 'angry')->toBeTrue();
    expect($r2->reaction === 'angry')->toBeTrue();
});

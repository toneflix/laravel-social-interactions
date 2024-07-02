<?php

use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use ToneflixCode\SocialInteractions\Tests\Models\Post;

test('can save item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $post->toggleSave($user);

    expect($r1->saved)->toBeTrue();
    expect($post->isSaved($user))->toBeTrue();
});

test('can unsave a saved item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $post->toggleSave($user);

    expect($r1->saved)->toBeTrue();
    expect($post->isSaved($user))->toBeTrue();

    $r2 = $post->toggleSave($user, false);

    expect($r2->saved)->toBeFalse();
    expect($post->isSaved($user))->toBeFalse();
});

test('can save item to list', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();
    $list = 'default';

    $r1 = $post->toggleSaveToList($user, true, $list);

    expect($r1->saveable->is($post))->tobeTrue();
    expect($post->isSaved($user, $list))->toBeTrue();
});

test('can list saved items in a list', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $list = 'default';

    Post::factory()->create()->toggleSaveToList($user, true, $list);
    Post::factory()->create()->toggleSaveToList($user, true, $list);
    Post::factory()->create()->toggleSaveToList($user, true, $list);
    Post::factory()->create()->toggleSaveToList($user, true, $list);

    expect($user->savedSocialInteracts()->list($list)->count())->tobe(4);
});
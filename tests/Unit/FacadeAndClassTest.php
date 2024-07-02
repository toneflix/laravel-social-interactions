<?php

use ToneflixCode\SocialInteractions\Facades\SocialInteractions;
use ToneflixCode\SocialInteractions\Tests\Models\Post;

test('can like item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    config(['social-interactions.enable_reactions' => false]);

    $r1 = SocialInteractions::leaveReaction($user, Post::factory()->create(), 1);
    $r2 = SocialInteractions::leaveReaction($user, Post::factory()->create(), true);
    $r3 = SocialInteractions::leaveReaction($user, Post::factory()->create(), 'like');

    expect($r1->liked)->toBeTrue();
    expect($r2->liked)->toBeTrue();
    expect($r3->liked)->toBeTrue();
});

test('can save item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = SocialInteractions::toggleSave($post, $user);

    expect($r1->saved)->toBeTrue();
    expect(SocialInteractions::isSaved($post, $user))->toBeTrue();
});

test('can save item to list', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();
    $list = 'default';

    $r1 = SocialInteractions::toggleSaveToList($post, $user, true, $list);

    expect($r1->saveable->is($post))->tobeTrue();
    expect(SocialInteractions::isSaved($post, $user, $list))->toBeTrue();
});

test('can delete a saved list', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'dance');

    $lists = $user->saved_social_lists;
    expect($lists->count())->toBe(2);

    SocialInteractions::deleteSavedSocialList($user, 'reusable');

    $user = $user->refresh();
    $lists = $user->saved_social_lists;
    expect($lists->count())->toBe(1);
});

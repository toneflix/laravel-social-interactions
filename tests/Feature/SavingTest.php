<?php

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

    expect($r1->savedItem->saveable->is($post))->tobeTrue();
    expect($post->isSaved($user, $list))->toBeTrue();
});

test('can unsave item from list', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();
    $list = 'default';

    $r1 = $post->toggleSaveToList($user, true, $list);
    expect($post->isSaved($user, $list))->toBeTrue();
    expect($r1->savedItem->saveable->is($post))->tobeTrue();

    $r2 = $post->toggleSaveToList($user, false, $list);
    expect($r2->savedItem)->tobeNull();
    expect($post->isSaved($user, $list))->toBeFalse();
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

test('can get all saving list', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    Post::factory()->create()->toggleSaveToList($user, true, 'default');
    Post::factory()->create()->toggleSaveToList($user, true, 'default');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'dance');

    $lists = $user->saved_social_lists;

    expect($lists->count())->toBe(3);
    expect($lists->contains('dance'))->toBeTrue();
    expect($lists->contains('default'))->toBeTrue();
    expect($lists->contains('reusable'))->toBeTrue();
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

    $user->deleteSavedSocialList('reusable');

    $user = $user->refresh();
    $lists = $user->saved_social_lists;
    expect($lists->count())->toBe(1);
});

test('can delete all saved lists', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'dance');

    $user->deleteSavedSocialList(true);

    $lists = $user->saved_social_lists;
    expect($lists->count())->toBe(0);
});

test('can delete all saved lists by list name', function () {

    config(['social-interactions.enable_save_lists' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'reusable');
    Post::factory()->create()->toggleSaveToList($user, true, 'dance');

    $user->deleteSavedSocialList('reusable');

    $lists = $user->saved_social_lists;
    expect($lists->count())->toBe(1);
});

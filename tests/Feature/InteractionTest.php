<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use ToneflixCode\SocialInteractions\Events\SocialInteractionDone;
use ToneflixCode\SocialInteractions\Tests\Models\Post;

test('can like item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    config(['social-interactions.enable_reactions' => false]);

    $r1 = $user->leaveReaction(Post::factory()->create(), 1);
    $r2 = $user->leaveReaction(Post::factory()->create(), true);
    $r3 = $user->leaveReaction(Post::factory()->create(), 'like');

    expect($r1->liked)->toBeTrue();
    expect($r2->liked)->toBeTrue();
    expect($r3->liked)->toBeTrue();
});

test('can unlike item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    config(['social-interactions.enable_reactions' => false]);

    $r1 = $user->leaveReaction(Post::factory()->create(), false);
    $r2 = $user->leaveReaction(Post::factory()->create(), 0);

    expect($r1->liked)->toBeFalse();
    expect($r2->liked)->toBeFalse();
});

test('can dislike item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 'dislike');

    expect($r1->disliked)->toBeTrue();
});

test('can undislike item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $user->leaveReaction($post, 'dislike');
    $r2 = $user->leaveReaction($post, 'dislike');

    expect($r1->disliked)->toBeTrue();
    expect($r2->disliked)->toBeFalse();
    expect($r1->id)->toBe($r1->id);
});

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

test('can vote item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $post->giveVote($user);

    expect($r1->votes > 0)->toBeTrue();
    expect($post->isVoted($user))->toBeTrue();
});

test('can vote item multiple times', function () {

    config(['social-interactions.multiple_votes' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $post->giveVote($user);
    $post->giveVote($user);
    $post->giveVote($user);
    $r1 = $post->giveVote($user);

    expect($r1->votes)->toBe(4);
    expect($post->isVoted($user))->toBeTrue();
});

test('can unvote item', function () {

    config(['social-interactions.enable_unvote' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $post->giveVote($user);
    $r1 = $post->giveVote($user, false);

    expect($r1->votes)->toBe(0);
    expect($post->isVoted($user))->toBeFalse();
});

test('Uses only one interaction model', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $user->leaveReaction($post, 1);
    $r2 = $post->toggleSave($user, true);

    expect($r1->id)->toBe($r2->id);
});

test('SocialInteractionDone event is dispatched', function () {
    Event::fake([SocialInteractionDone::class]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $user->leaveReaction(Post::factory()->create(), true);

    Event::assertDispatched(SocialInteractionDone::class, function ($event) {
        return $event->action === 'liked';
    });
});

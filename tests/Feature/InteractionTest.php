<?php

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

    config(['social-interactions.enable_dislikes' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $r1 = $user->leaveReaction(Post::factory()->create(), 'dislike');

    expect($r1->disliked)->toBeTrue();
});

test('can undislike item', function () {

    config(['social-interactions.enable_dislikes' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $user->leaveReaction($post, 'dislike');
    $r2 = $user->leaveReaction($post, 'dislike');

    expect($r1->disliked)->toBeTrue();
    expect($r2->disliked)->toBeFalse();
    expect($r1->id)->toBe($r1->id);
});

test('can vote item', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $post->giveVote($user);

    expect($r1->votes > 0)->toBeTrue();
    expect($post->isVoted($user))->toBeTrue();
});

test('can unvote item', function () {

    config(['social-interactions.enable_unvote' => true]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $post->giveVote($user);
    $r2 = $post->giveVote($user, false);

    expect($r1->votes)->toBe(1);
    expect($r2->votes)->toBe(0);
    expect($post->isVoted($user))->toBeFalse();
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

test('Uses only one interaction model', function () {

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $r1 = $user->leaveReaction($post, 1);
    $r2 = $post->toggleSave($user, true);

    expect($r1->is($r2))->toBeTrue();
});

test('SocialInteractionDone event is dispatched', function () {
    Event::fake([SocialInteractionDone::class]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();

    $user->leaveReaction(Post::factory()->create(), true);

    Event::assertDispatched(SocialInteractionDone::class, function ($event) {
        return $event->action === 'liked';
    });
});

test('Is able to generate interaction data for the model', function () {

    config([
        'social-interactions.enable_dislikes' => true,
        'social-interactions.enable_reactions' => true,
        'social-interactions.multiple_votes' => true,
    ]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $user->leaveReaction($post, 'like');
    $post->toggleSave($user, true);
    $post->giveVote($user);

    $user2 = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $user2->leaveReaction($post, 'love');
    $post->giveVote($user2);
    $post->giveVote($user2);

    $user3 = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $user3->leaveReaction($post, 'dislike');
    $post->giveVote($user3);
    $post->giveVote($user3);
    $post->giveVote($user3);

    $user4 = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $user4->leaveReaction($post, 'like');

    expect(isset($post->socialInteractionData($user3)['votes']))->toBeTrue();
    expect($post->socialInteractionData($user3)['voted'])->toBeTrue();
});

test('Is able to generate interaction data for the model without a user', function () {

    config([
        'social-interactions.enable_dislikes' => true,
        'social-interactions.enable_reactions' => true,
        'social-interactions.multiple_votes' => true,
    ]);

    $user = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $post = Post::factory()->create();

    $user->leaveReaction($post, 'like');
    $post->toggleSave($user, true);
    $post->giveVote($user);

    $user2 = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $user2->leaveReaction($post, 'love');
    $post->giveVote($user2);
    $post->giveVote($user2);

    $user4 = \ToneflixCode\SocialInteractions\Tests\Models\User::factory()->create();
    $user4->leaveReaction($post, 'like');

    expect(isset($post->socialInteractionData()['votes']))->toBeTrue();
    expect($post->socialInteractionData()['voted'] ?? 'unset')->toBe('unset');
});
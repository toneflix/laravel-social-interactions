<?php

namespace ToneflixCode\SocialInteractions\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ToneflixCode\SocialInteractions\Tests\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
        ];
    }
}

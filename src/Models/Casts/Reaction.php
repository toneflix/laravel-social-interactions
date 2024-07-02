<?php

namespace ToneflixCode\SocialInteractions\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Reaction implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param int|bool|string $val
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function get(Model $model, string $key, mixed $val, array $attributes): ?string
    {
        return config('social-interactions.enable_reactions', false)
            ? collect(config('social-interactions.available_reactions', []))->firstWhere(fn ($v, $i) => ($i + 1) == $val)
            : ($model->liked ? 'like' : null);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param int|bool|string $val
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $val, array $attributes): ?string
    {
        if (config('social-interactions.enable_reactions', false)) {
            if (is_bool($val)) {
                $reaction = $val ? 1 : 0;
            } elseif (is_string($val)) {
                $reactions = config('social-interactions.available_reactions', []);
                $reaction = in_array($val, $reactions) ? array_key_first(Arr::where(
                    $reactions,
                    fn ($v) => $v === $val
                )) + 1 : null;
            } else {
                $reaction = $val;
            }

            return (int)$reaction;
        }

        return NULL;
    }
}
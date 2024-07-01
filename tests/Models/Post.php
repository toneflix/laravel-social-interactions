<?php

namespace ToneflixCode\SocialInteractions\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ToneflixCode\SocialInteractions\Traits\HasSocialInteractions;

class Post extends Model
{
    use HasFactory;
    use HasSocialInteractions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
    ];
}

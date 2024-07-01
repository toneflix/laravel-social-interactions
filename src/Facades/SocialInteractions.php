<?php

namespace ToneflixCode\SocialInteractions\Facades;

use Illuminate\Support\Facades\Facade;

class SocialInteractions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'social-interactions';
    }
}
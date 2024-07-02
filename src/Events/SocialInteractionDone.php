<?php

namespace ToneflixCode\SocialInteractions\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;
use ToneflixCode\SocialInteractions\Models\SocialInteractionSave;

class SocialInteractionDone implements ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SocialInteraction|SocialInteractionSave $interaction,
        public string $action,
    ) {}
}

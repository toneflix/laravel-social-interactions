<?php

namespace ToneflixCode\SocialInteractions\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use ToneflixCode\SocialInteractions\Models\SocialInteraction;

class SocialInteractionDone implements ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SocialInteraction $interaction,
        public string $action,
    ) {
    }
}

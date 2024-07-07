<?php

return [
    'tables' => [
        'interactions' => 'social_interactions',
        'saves' => 'social_interaction_saves',
    ],

    'multiple_votes' => false,
    'enable_unvote' => false,
    'enable_dislikes' => false,
    'enable_reactions' => false,
    'enable_save_lists' => false,

    'available_reactions' => [
        'like',
        'love',
        'haha',
        'wow',
        'sad',
        'angry',
    ],

    /**
     * ==========================================================
     * Key maps will be used to represent your data whenever possible
     * ==========================================================
     */
    'key_maps' => [
        'vote' => 'vote',
        'like' => 'like',
        'save' => 'save',
        'react' => 'react',
        'dislike' => 'dislike',
        'reaction' => 'reaction',
        // 'saved' => 'saved',
        // 'voted' => 'voted',
        // 'disliked' => 'disliked',
    ],

    /**
     * ==========================================================
     * This should be equivalent to any icon library you're using
     * Here we have used font awesome icon as default
     * ==========================================================
     */
    'icon_classes' => [
        // These should map to your available reactions
        'love' => 'fas fa-heart',
        'haha' => 'fas fa-face-laugh',
        'wow' => 'fas fa-face-surprise',
        'sad' => 'fas fa-face-sad-tear',
        'angry' => 'fas fa-face-angry',

        // These will map to the other interactions
        'like' => ['far fa-thumbs-up', 'fas fa-thumbs-up'],
        'save' => ['far fa-bookmark', 'fas fa-bookmark'],
        'vote' => ['far fa-check-circle', 'fas fa-check-circle'],
        'dislike' => ['far fa-thumbs-down', 'fas fa-thumbs-down'],
    ],

    /**
     * =============================================================
     * This should be equivalent to a predefined class name or color
     * name depending on your specific frontend implementation needs
     * =============================================================
     */
    'reaction_colors' => [
        // These should map to your available reactions
        'like' => 'blue',
        'love' => 'red',
        'haha' => 'yellow',
        'wow' => 'yellow',
        'sad' => 'yellow',
        'angry' => 'orange',
    ],
];

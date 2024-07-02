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
     * This should be equivalent to any icon library you're using
     * Here we have used font awesome icon as default
     * ==========================================================
     */
    'icon_classes' => [
        'love' => 'fas fa-heart',
        'haha' => 'fas fa-face-laugh',
        'wow' => 'fas fa-face-surprise',
        'sad' => 'fas fa-face-sad-tear',
        'angry' => 'fas fa-face-angry',

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
        'love' => 'red',
        'haha' => 'yellow',
        'wow' => 'yellow',
        'sad' => 'yellow',
        'angry' => 'orange',

        'like' => ['far fa-thumbs-up', 'fas fa-thumbs-up'],
        'save' => ['far fa-bookmark', 'fas fa-bookmark'],
        'vote' => ['far fa-check-circle', 'fas fa-check-circle'],
        'dislike' => ['far fa-thumbs-down', 'fas fa-thumbs-down'],
    ],
];

<?php

namespace ToneflixCode\SocialInteractions;

final class Helpers
{
    public static function convertToPastTense($word)
    {
        // Irregular verbs
        $irregularVerbs = [
            'be' => 'was',
            'become' => 'became',
            'begin' => 'began',
            'break' => 'broke',
            'bring' => 'brought',
            'buy' => 'bought',
            'choose' => 'chose',
            'come' => 'came',
            'do' => 'did',
            'drink' => 'drank',
            'eat' => 'ate',
            'fall' => 'fell',
            'find' => 'found',
            'fly' => 'flew',
            'forget' => 'forgot',
            'get' => 'got',
            'give' => 'gave',
            'go' => 'went',
            'have' => 'had',
            'hear' => 'heard',
            'know' => 'knew',
            'make' => 'made',
            'meet' => 'met',
            'run' => 'ran',
            'say' => 'said',
            'see' => 'saw',
            'sell' => 'sold',
            'send' => 'sent',
            'sing' => 'sang',
            'sit' => 'sat',
            'speak' => 'spoke',
            'stand' => 'stood',
            'take' => 'took',
            'teach' => 'taught',
            'tell' => 'told',
            'think' => 'thought',
            'write' => 'wrote',
        ];

        // Check if the word is irregular
        if (array_key_exists($word, $irregularVerbs)) {
            return $irregularVerbs[$word];
        }

        // Regular verb rules
        $lastChar = substr($word, -1);
        $lastTwoChars = substr($word, -2);

        if ($lastChar == 'e') {
            return $word . 'd';
        } elseif ($lastTwoChars == 'y' && !in_array(substr($word, -3, 1), ['a', 'e', 'i', 'o', 'u'])) {
            return substr($word, 0, -1) . 'ied';
        } elseif (in_array($lastChar, ['a', 'i', 'o', 'u', 'l', 'r', 'n'])) {
            return $word . $lastChar . 'ed';
        } else {
            return $word . 'ed';
        }
    }
}

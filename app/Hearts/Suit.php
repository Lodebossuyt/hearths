<?php

namespace App\Hearts;

enum Suit: string
{
    case Clubs = 'C';
    case Diamonds = 'D';
    case Spades = 'S';
    case Hearts = 'H';

    /**
     * The order suits are displayed in a Hand, alternating colours.
     */
    public function sortOrder(): int
    {
        return match ($this) {
            self::Clubs => 0,
            self::Diamonds => 1,
            self::Spades => 2,
            self::Hearts => 3,
        };
    }
}

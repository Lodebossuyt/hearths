<?php

namespace App\Hearts;

enum Rank: int
{
    case Two = 2;
    case Three = 3;
    case Four = 4;
    case Five = 5;
    case Six = 6;
    case Seven = 7;
    case Eight = 8;
    case Nine = 9;
    case Ten = 10;
    case Jack = 11;
    case Queen = 12;
    case King = 13;
    case Ace = 14;

    public function symbol(): string
    {
        return match ($this) {
            self::Ten => 'T',
            self::Jack => 'J',
            self::Queen => 'Q',
            self::King => 'K',
            self::Ace => 'A',
            default => (string) $this->value,
        };
    }

    public static function fromSymbol(string $symbol): self
    {
        return match ($symbol) {
            'T' => self::Ten,
            'J' => self::Jack,
            'Q' => self::Queen,
            'K' => self::King,
            'A' => self::Ace,
            default => self::from((int) $symbol),
        };
    }
}

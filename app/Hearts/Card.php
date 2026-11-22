<?php

namespace App\Hearts;

use InvalidArgumentException;

/**
 * A single playing card, identified by a two-character code such as "QS" or "TH".
 */
final readonly class Card
{
    public function __construct(public Rank $rank, public Suit $suit) {}

    public static function fromCode(string $code): self
    {
        if (strlen($code) !== 2) {
            throw new InvalidArgumentException("Invalid card code [{$code}].");
        }

        return new self(Rank::fromSymbol($code[0]), Suit::from($code[1]));
    }

    public static function isValidCode(string $code): bool
    {
        return (bool) preg_match('/^[2-9TJQKA][CDSH]$/', $code);
    }

    public static function twoOfClubs(): self
    {
        return new self(Rank::Two, Suit::Clubs);
    }

    /**
     * @return list<self>
     */
    public static function deck(): array
    {
        $deck = [];

        foreach (Suit::cases() as $suit) {
            foreach (Rank::cases() as $rank) {
                $deck[] = new self($rank, $suit);
            }
        }

        return $deck;
    }

    public function code(): string
    {
        return $this->rank->symbol().$this->suit->value;
    }

    public function points(): int
    {
        return match (true) {
            $this->suit === Suit::Hearts => 1,
            $this->isQueenOfSpades() => 13,
            default => 0,
        };
    }

    public function isPointCard(): bool
    {
        return $this->points() > 0;
    }

    public function isQueenOfSpades(): bool
    {
        return $this->suit === Suit::Spades && $this->rank === Rank::Queen;
    }

    public function equals(self $other): bool
    {
        return $this->code() === $other->code();
    }

    public function sortKey(): int
    {
        return $this->suit->sortOrder() * 100 + $this->rank->value;
    }
}

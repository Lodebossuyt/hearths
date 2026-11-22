<?php

namespace App\Hearts;

/**
 * The pure rules of Hearts: which cards may be played, who takes a Trick and how a Round scores.
 */
final class Rules
{
    public const int GAME_END_SCORE = 100;

    public const int MOON_POINTS = 26;

    /**
     * Determine which cards from a Hand may legally be played next.
     *
     * @param  list<Card>  $hand
     * @param  list<Card>  $trickSoFar  Cards already played to the current Trick, in order.
     * @return list<Card>
     */
    public static function playableCards(array $hand, array $trickSoFar, bool $isFirstTrick, bool $heartsBroken): array
    {
        if ($hand === []) {
            return [];
        }

        if ($trickSoFar === []) {
            return self::leadableCards($hand, $isFirstTrick, $heartsBroken);
        }

        $ledSuit = $trickSoFar[0]->suit;
        $following = array_values(array_filter($hand, fn (Card $card): bool => $card->suit === $ledSuit));

        if ($following !== []) {
            return $following;
        }

        if ($isFirstTrick) {
            $harmless = array_values(array_filter($hand, fn (Card $card): bool => ! $card->isPointCard()));

            if ($harmless !== []) {
                return $harmless;
            }
        }

        return $hand;
    }

    /**
     * @param  list<Card>  $hand
     * @return list<Card>
     */
    private static function leadableCards(array $hand, bool $isFirstTrick, bool $heartsBroken): array
    {
        if ($isFirstTrick) {
            return array_values(array_filter($hand, fn (Card $card): bool => $card->equals(Card::twoOfClubs())));
        }

        if ($heartsBroken) {
            return $hand;
        }

        $nonHearts = array_values(array_filter($hand, fn (Card $card): bool => $card->suit !== Suit::Hearts));

        return $nonHearts !== [] ? $nonHearts : $hand;
    }

    /**
     * @param  list<Card>  $hand
     * @param  list<Card>  $trickSoFar
     */
    public static function isPlayable(Card $card, array $hand, array $trickSoFar, bool $isFirstTrick, bool $heartsBroken): bool
    {
        foreach (self::playableCards($hand, $trickSoFar, $isFirstTrick, $heartsBroken) as $playable) {
            if ($playable->equals($card)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the index (0-3, in play order) of the card that takes a complete Trick.
     *
     * @param  list<Card>  $trick
     */
    public static function winningIndex(array $trick): int
    {
        $ledSuit = $trick[0]->suit;
        $winningIndex = 0;

        foreach ($trick as $index => $card) {
            if ($card->suit === $ledSuit && $card->rank->value > $trick[$winningIndex]->rank->value) {
                $winningIndex = $index;
            }
        }

        return $winningIndex;
    }

    /**
     * The raw Points each Seat position took in a Round, before Shooting the moon is applied.
     *
     * @param  array<int, list<Card>>  $cardsTakenByPosition
     * @return array<int, int>
     */
    public static function rawPoints(array $cardsTakenByPosition): array
    {
        $points = [0 => 0, 1 => 0, 2 => 0, 3 => 0];

        foreach ($cardsTakenByPosition as $position => $cards) {
            foreach ($cards as $card) {
                $points[$position] += $card->points();
            }
        }

        return $points;
    }

    /**
     * The Seat position that Shot the moon, if any.
     *
     * @param  array<int, int>  $rawPoints
     */
    public static function moonShooter(array $rawPoints): ?int
    {
        foreach ($rawPoints as $position => $points) {
            if ($points === self::MOON_POINTS) {
                return $position;
            }
        }

        return null;
    }

    /**
     * The Points added to each Seat's Score for a finished Round.
     *
     * @param  array<int, int>  $rawPoints
     * @return array<int, int>
     */
    public static function roundScores(array $rawPoints): array
    {
        $shooter = self::moonShooter($rawPoints);

        if ($shooter === null) {
            return $rawPoints;
        }

        $scores = [];

        foreach (array_keys($rawPoints) as $position) {
            $scores[$position] = $position === $shooter ? 0 : self::MOON_POINTS;
        }

        return $scores;
    }

    /**
     * @param  array<int, int>  $scores
     */
    public static function isGameOver(array $scores): bool
    {
        if ($scores === []) {
            return false;
        }

        return max($scores) >= self::GAME_END_SCORE;
    }

    /**
     * The positions holding the lowest Score; several when tied.
     *
     * @param  array<int, int>  $scores
     * @return list<int>
     */
    public static function winners(array $scores): array
    {
        if ($scores === []) {
            return [];
        }

        $lowest = min($scores);

        return array_keys(array_filter($scores, fn (int $score): bool => $score === $lowest));
    }
}

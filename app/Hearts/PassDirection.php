<?php

namespace App\Hearts;

enum PassDirection: string
{
    case Left = 'left';
    case Right = 'right';
    case Across = 'across';
    case None = 'none';

    /**
     * The pass direction rotates left, right, across, none for each Round.
     */
    public static function forRound(int $roundNumber): self
    {
        return [self::Left, self::Right, self::Across, self::None][($roundNumber - 1) % 4];
    }

    /**
     * The Seat position that receives the cards passed from the given position.
     */
    public function recipient(int $position): int
    {
        return match ($this) {
            self::Left => ($position + 1) % 4,
            self::Right => ($position + 3) % 4,
            self::Across => ($position + 2) % 4,
            self::None => $position,
        };
    }
}

<?php

namespace App\Hearts;

enum RoomStatus: string
{
    case Waiting = 'waiting';
    case Playing = 'playing';
    case Finished = 'finished';
    case Abandoned = 'abandoned';

    public function isActive(): bool
    {
        return $this === self::Waiting || $this === self::Playing;
    }
}

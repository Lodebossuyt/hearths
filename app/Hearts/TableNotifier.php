<?php

namespace App\Hearts;

use App\Events\LobbyUpdated;
use App\Events\RoomUpdated;
use App\Models\Room;

/**
 * Tells connected browsers that a Room or the Lobby changed so their live props reload.
 * A broadcasting failure is reported but never breaks the Player's action.
 */
final class TableNotifier
{
    public static function roomChanged(Room $room): void
    {
        rescue(function () use ($room): void {
            broadcast(RoomUpdated::for($room))->toOthers();
        });
    }

    public static function lobbyChanged(): void
    {
        rescue(function (): void {
            broadcast(new LobbyUpdated);
        });
    }
}

<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Signals that something in a Room changed. It deliberately carries no game data:
 * each seated Player reloads their own private view of the Game.
 */
class RoomUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public string $roomCode) {}

    public static function for(Room $room): self
    {
        return new self($room->code);
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('room.'.$this->roomCode);
    }

    public function broadcastAs(): string
    {
        return 'room.updated';
    }

    /**
     * @return array{code: string}
     */
    public function broadcastWith(): array
    {
        return ['code' => $this->roomCode];
    }
}

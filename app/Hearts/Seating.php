<?php

namespace App\Hearts;

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Manages who sits in which Room: creating Rooms, joining, leaving and abandoning.
 */
final class Seating
{
    public function __construct(private GameEngine $engine) {}

    public function createRoom(User $user): Room
    {
        $room = DB::transaction(function () use ($user): Room {
            $this->ensureNotSeatedElsewhere($user);

            $room = Room::query()->create([
                'code' => Room::generateUniqueCode(),
                'status' => RoomStatus::Waiting,
            ]);

            $room->seats()->create(['user_id' => $user->id, 'position' => 0]);

            return $room;
        });

        TableNotifier::lobbyChanged();

        return $room;
    }

    public function join(Room $room, User $user): void
    {
        DB::transaction(function () use ($room, $user): void {
            $room = $this->lock($room);

            if ($room->seatFor($user) !== null) {
                return;
            }

            if ($room->status !== RoomStatus::Waiting || $room->isFull()) {
                throw new IllegalMove('This room is no longer open.');
            }

            $this->ensureNotSeatedElsewhere($user);

            $room->seats()->create([
                'user_id' => $user->id,
                'position' => $room->lowestFreePosition(),
            ]);

            $room->load('seats');

            if ($room->isFull()) {
                $this->engine->startGame($room);
            }
        });

        TableNotifier::roomChanged($room);
        TableNotifier::lobbyChanged();
    }

    public function leave(Room $room, User $user): void
    {
        DB::transaction(function () use ($room, $user): void {
            $room = $this->lock($room);
            $seat = $room->seatFor($user);

            if ($seat === null) {
                return;
            }

            if ($room->status !== RoomStatus::Waiting) {
                throw new IllegalMove('The game has started. Abandon it instead.');
            }

            $seat->delete();

            if ($room->seats()->doesntExist()) {
                $room->delete();
            }
        });

        TableNotifier::roomChanged($room);
        TableNotifier::lobbyChanged();
    }

    public function abandon(Room $room, User $user): void
    {
        DB::transaction(function () use ($room, $user): void {
            $room = $this->lock($room);

            if ($room->seatFor($user) === null) {
                throw new IllegalMove('You are not seated in this room.');
            }

            if ($room->status !== RoomStatus::Playing) {
                throw new IllegalMove('Only a game in progress can be abandoned.');
            }

            $room->update([
                'status' => RoomStatus::Abandoned,
                'abandoned_by_user_id' => $user->id,
                'ended_at' => now(),
            ]);
        });

        TableNotifier::roomChanged($room);
    }

    private function lock(Room $room): Room
    {
        return Room::query()->with('seats')->lockForUpdate()->findOrFail($room->id);
    }

    private function ensureNotSeatedElsewhere(User $user): void
    {
        if ($user->activeRoom() !== null) {
            throw new IllegalMove('You are already seated at another hearth.');
        }
    }
}

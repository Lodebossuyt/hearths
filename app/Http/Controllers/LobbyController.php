<?php

namespace App\Http\Controllers;

use App\Events\LobbyUpdated;
use App\Hearts\RoomStatus;
use App\Models\Room;
use App\Models\Seat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LobbyController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Lobby', [
            'rooms' => Inertia::live(fn (): array => $this->waitingRooms(), on: LobbyUpdated::class),
            'currentRoomCode' => Inertia::live(fn (): ?string => $user?->activeRoom()?->code, on: LobbyUpdated::class),
        ]);
    }

    /**
     * @return list<array{code: string, players: list<string>, freeSeats: int}>
     */
    private function waitingRooms(): array
    {
        return array_values(Room::query()
            ->where('status', RoomStatus::Waiting)
            ->with('seats.user')
            ->latest()
            ->get()
            ->map(fn (Room $room): array => [
                'code' => $room->code,
                'players' => array_values($room->seats->map(fn (Seat $seat): string => $seat->user->name)->all()),
                'freeSeats' => Room::SEAT_COUNT - $room->seats->count(),
            ])
            ->all());
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\RoomUpdated;
use App\Hearts\PlayerView;
use App\Hearts\RoomStatus;
use App\Hearts\Seating;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function store(Request $request, Seating $seating): RedirectResponse
    {
        $room = $seating->createRoom($request->user());

        return redirect()->route('rooms.show', $room);
    }

    public function show(Request $request, Room $room): Response|RedirectResponse
    {
        $user = $request->user();
        $isSeated = $room->hasSeated($user);

        if (! $isSeated && $room->status !== RoomStatus::Waiting) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'That game is not open to visitors.']);

            return redirect()->route('lobby');
        }

        $table = fn (): array => PlayerView::for($room->fresh() ?? $room, $user);

        return Inertia::render('rooms/Show', [
            'table' => $isSeated ? Inertia::live($table, on: RoomUpdated::for($room)) : $table,
        ]);
    }
}

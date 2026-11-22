<?php

namespace App\Http\Controllers;

use App\Hearts\Seating;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomSeatController extends Controller
{
    public function store(Request $request, Room $room, Seating $seating): RedirectResponse
    {
        $seating->join($room, $request->user());

        return redirect()->route('rooms.show', $room);
    }

    public function destroy(Request $request, Room $room, Seating $seating): RedirectResponse
    {
        $seating->leave($room, $request->user());

        return redirect()->route('lobby');
    }
}

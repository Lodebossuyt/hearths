<?php

namespace App\Http\Controllers;

use App\Hearts\Seating;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AbandonGameController extends Controller
{
    public function __invoke(Request $request, Room $room, Seating $seating): RedirectResponse
    {
        $seating->abandon($room, $request->user());

        return redirect()->route('rooms.show', $room);
    }
}

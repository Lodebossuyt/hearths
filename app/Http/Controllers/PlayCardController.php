<?php

namespace App\Http\Controllers;

use App\Hearts\GameEngine;
use App\Http\Requests\PlayCardRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;

class PlayCardController extends Controller
{
    public function __invoke(PlayCardRequest $request, Room $room, GameEngine $engine): RedirectResponse
    {
        $engine->play($room, $request->user(), $request->string('card')->toString());

        return redirect()->route('rooms.show', $room);
    }
}

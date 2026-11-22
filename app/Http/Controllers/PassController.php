<?php

namespace App\Http\Controllers;

use App\Hearts\GameEngine;
use App\Http\Requests\PassCardsRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;

class PassController extends Controller
{
    public function __invoke(PassCardsRequest $request, Room $room, GameEngine $engine): RedirectResponse
    {
        $engine->pass($room, $request->user(), $request->cardCodes());

        return redirect()->route('rooms.show', $room);
    }
}

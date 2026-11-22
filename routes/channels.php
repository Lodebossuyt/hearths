<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
 * The private channel live props listen on for "this Room changed" signals.
 */
Broadcast::channel('room.{code}', function (User $user, string $code): bool {
    return Room::query()->where('code', $code)->first()?->hasSeated($user) ?? false;
});

/*
 * The presence channel that tells seated Players who is connected and who is Away.
 */
Broadcast::channel('table.{code}', function (User $user, string $code): array|false {
    $isSeated = Room::query()->where('code', $code)->first()?->hasSeated($user) ?? false;

    return $isSeated ? ['id' => $user->id, 'name' => $user->name] : false;
});

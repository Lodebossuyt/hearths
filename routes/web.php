<?php

use App\Http\Controllers\AbandonGameController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\PassController;
use App\Http\Controllers\PlayCardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomSeatController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/lobby')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('lobby', [LobbyController::class, 'index'])->name('lobby');

    Route::post('rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('rooms/{room}/seat', [RoomSeatController::class, 'store'])->name('rooms.seat.store');
    Route::delete('rooms/{room}/seat', [RoomSeatController::class, 'destroy'])->name('rooms.seat.destroy');
    Route::post('rooms/{room}/abandon', AbandonGameController::class)->name('rooms.abandon');
    Route::post('rooms/{room}/pass', PassController::class)->name('rooms.pass');
    Route::post('rooms/{room}/play', PlayCardController::class)->name('rooms.play');
});

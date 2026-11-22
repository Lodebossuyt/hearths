<?php

namespace App\Hearts;

use App\Models\Room;
use App\Models\Round;
use App\Models\RoundCard;
use App\Models\Seat;
use App\Models\Trick;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Applies Player actions to a Game: dealing Rounds, the Pass, playing cards and scoring.
 * Every public action locks the Room row so simultaneous actions are applied one at a time.
 */
final class GameEngine
{
    private const int CARDS_PER_PASS = 3;

    private const int TRICKS_PER_ROUND = 13;

    /**
     * Start the Game in a Room whose four Seats are filled. Must run inside a transaction.
     */
    public function startGame(Room $room): void
    {
        $room->update(['status' => RoomStatus::Playing, 'started_at' => now()]);

        $this->dealRound($room, 1);
    }

    /**
     * @param  list<string>  $cardCodes
     */
    public function pass(Room $room, User $user, array $cardCodes): void
    {
        DB::transaction(function () use ($room, $user, $cardCodes): void {
            [$room, $seat, $round] = $this->lockForAction($room, $user);

            if ($round->status !== RoundStatus::Passing) {
                throw new IllegalMove('It is not time to pass cards.');
            }

            if ($round->hasPassed($seat->position)) {
                throw new IllegalMove('You have already passed your cards.');
            }

            $cardCodes = array_values(array_unique($cardCodes));

            if (count($cardCodes) !== self::CARDS_PER_PASS) {
                throw new IllegalMove('Choose exactly three cards to pass.');
            }

            $chosen = $round->cards
                ->filter(fn (RoundCard $card): bool => $card->held_by_position === $seat->position && in_array($card->card, $cardCodes, true));

            if ($chosen->count() !== self::CARDS_PER_PASS) {
                throw new IllegalMove('You can only pass cards from your own hand.');
            }

            $recipient = $round->pass_direction->recipient($seat->position);

            RoundCard::query()->whereKey($chosen->modelKeys())->update(['passed_to_position' => $recipient]);

            $round->load('cards');

            $everyoneHasPassed = collect(range(0, Room::SEAT_COUNT - 1))
                ->every(fn (int $position): bool => $round->hasPassed($position));

            if ($everyoneHasPassed) {
                $this->exchangePassedCards($round);
            }
        });

        TableNotifier::roomChanged($room);
    }

    public function play(Room $room, User $user, string $cardCode): void
    {
        DB::transaction(function () use ($room, $user, $cardCode): void {
            [$room, $seat, $round] = $this->lockForAction($room, $user);

            if ($round->status !== RoundStatus::Playing) {
                throw new IllegalMove('Cards cannot be played right now.');
            }

            $trick = $round->currentTrick;

            if ($trick === null || $trick->isComplete()) {
                throw new IllegalMove('Cards cannot be played right now.');
            }

            $plays = $trick->plays()->get();

            if (self::turnPosition($trick, $plays->count()) !== $seat->position) {
                throw new IllegalMove('It is not your turn.');
            }

            $roundCard = $round->cards->first(
                fn (RoundCard $card): bool => $card->card === $cardCode
                    && $card->held_by_position === $seat->position
                    && $card->trick_id === null,
            );

            if ($roundCard === null) {
                throw new IllegalMove('That card is not in your hand.');
            }

            $trickSoFar = array_values($plays->map(fn (RoundCard $card): Card => $card->toCard())->all());
            $card = $roundCard->toCard();

            if (! Rules::isPlayable($card, $round->handOf($seat->position), $trickSoFar, $trick->number === 1, $round->hearts_broken)) {
                throw new IllegalMove('That card can\'t be played.');
            }

            $roundCard->update(['trick_id' => $trick->id, 'play_order' => $plays->count()]);

            if ($card->suit === Suit::Hearts && ! $round->hearts_broken) {
                $round->update(['hearts_broken' => true]);
            }

            if ($plays->count() + 1 === Room::SEAT_COUNT) {
                $this->completeTrick($room, $round, $trick);
            }
        });

        TableNotifier::roomChanged($room);
    }

    /**
     * The Seat position whose turn it is within an open Trick.
     */
    public static function turnPosition(Trick $trick, int $cardsPlayed): int
    {
        return ($trick->leader_position + $cardsPlayed) % Room::SEAT_COUNT;
    }

    private function dealRound(Room $room, int $number): void
    {
        $direction = PassDirection::forRound($number);

        $round = $room->rounds()->create([
            'number' => $number,
            'pass_direction' => $direction,
            'status' => $direction === PassDirection::None ? RoundStatus::Playing : RoundStatus::Passing,
            'hearts_broken' => false,
        ]);

        $deck = Card::deck();
        shuffle($deck);
        $now = now();

        RoundCard::query()->insert(array_map(fn (Card $card, int $index): array => [
            'round_id' => $round->id,
            'card' => $card->code(),
            'dealt_to_position' => intdiv($index, self::TRICKS_PER_ROUND),
            'held_by_position' => intdiv($index, self::TRICKS_PER_ROUND),
            'created_at' => $now,
            'updated_at' => $now,
        ], $deck, array_keys($deck)));

        if ($direction === PassDirection::None) {
            $this->startFirstTrick($round);
        }
    }

    private function exchangePassedCards(Round $round): void
    {
        foreach ($round->cards as $card) {
            if ($card->passed_to_position !== null) {
                $card->update(['held_by_position' => $card->passed_to_position]);
            }
        }

        $round->update(['status' => RoundStatus::Playing]);

        $this->startFirstTrick($round);
    }

    private function startFirstTrick(Round $round): void
    {
        $leader = $round->cards()->where('card', Card::twoOfClubs()->code())->value('held_by_position');

        $round->tricks()->create(['number' => 1, 'leader_position' => $leader]);
    }

    private function completeTrick(Room $room, Round $round, Trick $trick): void
    {
        $plays = $trick->plays()->get();
        $winningIndex = Rules::winningIndex(array_values($plays->map(fn (RoundCard $card): Card => $card->toCard())->all()));
        $winner = ($trick->leader_position + $winningIndex) % Room::SEAT_COUNT;

        $trick->update(['winner_position' => $winner]);

        if ($trick->number < self::TRICKS_PER_ROUND) {
            $round->tricks()->create(['number' => $trick->number + 1, 'leader_position' => $winner]);

            return;
        }

        $this->completeRound($room, $round->fresh(['cards', 'tricks']) ?? $round);
    }

    private function completeRound(Room $room, Round $round): void
    {
        $roundScores = Rules::roundScores($round->pointsByPosition());

        foreach ($room->seats as $seat) {
            $seat->increment('score', $roundScores[$seat->position]);
        }

        $round->update(['status' => RoundStatus::Finished]);

        $scores = $room->seats->mapWithKeys(fn (Seat $seat): array => [$seat->position => $seat->score])->all();

        if (Rules::isGameOver($scores)) {
            $room->update(['status' => RoomStatus::Finished, 'ended_at' => now()]);

            return;
        }

        $this->dealRound($room, $round->number + 1);
    }

    /**
     * @return array{0: Room, 1: Seat, 2: Round}
     */
    private function lockForAction(Room $room, User $user): array
    {
        $room = Room::query()->with('seats')->lockForUpdate()->findOrFail($room->id);
        $seat = $room->seatFor($user);

        if ($seat === null) {
            throw new IllegalMove('You are not seated in this room.');
        }

        if ($room->status !== RoomStatus::Playing) {
            throw new IllegalMove('This game is not in progress.');
        }

        $round = $room->currentRound()->with(['cards', 'currentTrick'])->firstOrFail();

        return [$room, $seat, $round];
    }
}

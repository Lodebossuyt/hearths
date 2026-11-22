<?php

namespace App\Hearts;

use App\Models\Room;
use App\Models\Round;
use App\Models\RoundCard;
use App\Models\Seat;
use App\Models\Trick;
use App\Models\User;

/**
 * Builds what one Player is allowed to see of a Room: their own Hand and Points,
 * but only card counts and Scores for everyone else.
 *
 * @phpstan-type SeatView array{position: int, name: string, userId: int, score: int, cardCount: int, hasPassed: bool, isTurn: bool, isMe: bool}
 * @phpstan-type HandCard array{code: string, playable: bool, received: bool}
 * @phpstan-type TrickView array{number: int, plays: list<array{position: int, card: string}>, winnerPosition: int|null}
 * @phpstan-type RoundView array{number: int, passDirection: string, status: string, heartsBroken: bool, myPoints: int, hand: list<HandCard>, hasPassed: bool, passRecipientPosition: int|null, turnPosition: int|null, trick: TrickView|null}
 * @phpstan-type HistoryEntry array{number: int, scores: array<int, int>, moonShooter: int|null}
 */
final class PlayerView
{
    /**
     * @return array{code: string, status: string, myPosition: int|null, seats: list<SeatView>, round: RoundView|null, history: list<HistoryEntry>, winners: list<int>, abandonedBy: string|null}
     */
    public static function for(Room $room, User $user): array
    {
        $room->load(['seats.user', 'rounds.cards', 'rounds.tricks.plays', 'abandonedBy']);

        $mySeat = $room->seatFor($user);
        $round = $room->rounds->last();
        $turnPosition = $round !== null ? self::turnPosition($round) : null;

        return [
            'code' => $room->code,
            'status' => $room->status->value,
            'myPosition' => $mySeat?->position,
            'seats' => array_values($room->seats->map(fn (Seat $seat): array => [
                'position' => $seat->position,
                'name' => $seat->user->name,
                'userId' => $seat->user_id,
                'score' => $seat->score,
                'cardCount' => $round !== null ? count($round->handOf($seat->position)) : 0,
                'hasPassed' => $round !== null && $round->status === RoundStatus::Passing && $round->hasPassed($seat->position),
                'isTurn' => $turnPosition === $seat->position && $room->status === RoomStatus::Playing,
                'isMe' => $seat->user_id === $user->id,
            ])->all()),
            'round' => $round !== null && $mySeat !== null ? self::round($round, $mySeat, $turnPosition) : null,
            'history' => self::history($room),
            'winners' => $room->status === RoomStatus::Finished
                ? Rules::winners($room->seats->mapWithKeys(fn (Seat $seat): array => [$seat->position => $seat->score])->all())
                : [],
            'abandonedBy' => $room->abandonedBy?->name,
        ];
    }

    /**
     * @return RoundView
     */
    private static function round(Round $round, Seat $mySeat, ?int $turnPosition): array
    {
        $position = $mySeat->position;
        $hand = $round->handOf($position);
        $openTrick = self::openTrick($round);
        $isMyTurn = $turnPosition === $position && $round->status === RoundStatus::Playing && $openTrick !== null;

        $playable = $isMyTurn
            ? Rules::playableCards($hand, self::cardsOf($openTrick), $openTrick->number === 1, $round->hearts_broken)
            : [];
        $playableCodes = array_map(fn (Card $card): string => $card->code(), $playable);

        $firstTrick = $round->tricks->first();
        $showReceived = $round->pass_direction !== PassDirection::None
            && $firstTrick !== null
            && $firstTrick->plays->isEmpty();

        $receivedCodes = $round->cards
            ->filter(fn (RoundCard $card): bool => $card->held_by_position === $position && $card->dealt_to_position !== $position)
            ->pluck('card')
            ->all();

        return [
            'number' => $round->number,
            'passDirection' => $round->pass_direction->value,
            'status' => $round->status->value,
            'heartsBroken' => $round->hearts_broken,
            'myPoints' => $round->pointsByPosition()[$position],
            'hand' => array_map(fn (Card $card): array => [
                'code' => $card->code(),
                'playable' => in_array($card->code(), $playableCodes, true),
                'received' => $showReceived && in_array($card->code(), $receivedCodes, true),
            ], $hand),
            'hasPassed' => $round->hasPassed($position),
            'passRecipientPosition' => $round->pass_direction === PassDirection::None ? null : $round->pass_direction->recipient($position),
            'turnPosition' => $turnPosition,
            'trick' => self::visibleTrick($round),
        ];
    }

    private static function turnPosition(Round $round): ?int
    {
        $trick = self::openTrick($round);

        if ($round->status !== RoundStatus::Playing || $trick === null) {
            return null;
        }

        return GameEngine::turnPosition($trick, $trick->plays->count());
    }

    private static function openTrick(Round $round): ?Trick
    {
        $trick = $round->tricks->last();

        return $trick !== null && ! $trick->isComplete() ? $trick : null;
    }

    /**
     * The Trick on the table: the one being played, or the just-completed one until the next Lead.
     *
     * @return TrickView|null
     */
    private static function visibleTrick(Round $round): ?array
    {
        $trick = $round->tricks->last();

        if ($trick !== null && $trick->plays->isEmpty()) {
            $trick = $round->tricks->firstWhere('number', $trick->number - 1);
        }

        if ($trick === null) {
            return null;
        }

        return [
            'number' => $trick->number,
            'plays' => array_values($trick->plays->map(fn (RoundCard $card): array => [
                'position' => GameEngine::turnPosition($trick, (int) $card->play_order),
                'card' => $card->card,
            ])->all()),
            'winnerPosition' => $trick->winner_position,
        ];
    }

    /**
     * @return list<Card>
     */
    private static function cardsOf(Trick $trick): array
    {
        return array_values($trick->plays->map(fn (RoundCard $card): Card => $card->toCard())->all());
    }

    /**
     * @return list<HistoryEntry>
     */
    private static function history(Room $room): array
    {
        return array_values($room->rounds
            ->filter(fn (Round $round): bool => $round->status === RoundStatus::Finished)
            ->map(function (Round $round): array {
                $rawPoints = $round->pointsByPosition();

                return [
                    'number' => $round->number,
                    'scores' => Rules::roundScores($rawPoints),
                    'moonShooter' => Rules::moonShooter($rawPoints),
                ];
            })
            ->all());
    }
}

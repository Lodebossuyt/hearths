<?php

namespace App\Models;

use App\Hearts\Card;
use App\Hearts\PassDirection;
use App\Hearts\RoundStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * One deal of the cards: a Pass followed by 13 Tricks.
 *
 * @property int $id
 * @property int $room_id
 * @property int $number
 * @property PassDirection $pass_direction
 * @property RoundStatus $status
 * @property bool $hearts_broken
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Room $room
 * @property-read Collection<int, RoundCard> $cards
 * @property-read Collection<int, Trick> $tricks
 * @property-read Trick|null $currentTrick
 */
#[Fillable(['room_id', 'number', 'pass_direction', 'status', 'hearts_broken'])]
class Round extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'pass_direction' => PassDirection::class,
            'status' => RoundStatus::class,
            'hearts_broken' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return HasMany<RoundCard, $this>
     */
    public function cards(): HasMany
    {
        return $this->hasMany(RoundCard::class);
    }

    /**
     * @return HasMany<Trick, $this>
     */
    public function tricks(): HasMany
    {
        return $this->hasMany(Trick::class)->orderBy('number');
    }

    /**
     * @return HasOne<Trick, $this>
     */
    public function currentTrick(): HasOne
    {
        return $this->hasOne(Trick::class)->latestOfMany('number');
    }

    /**
     * The unplayed cards a Seat position currently holds.
     *
     * @return list<Card>
     */
    public function handOf(int $position): array
    {
        return array_values($this->cards
            ->filter(fn (RoundCard $card): bool => $card->held_by_position === $position && $card->trick_id === null)
            ->map(fn (RoundCard $card): Card => $card->toCard())
            ->sortBy(fn (Card $card): int => $card->sortKey())
            ->all());
    }

    public function hasPassed(int $position): bool
    {
        return $this->cards->contains(
            fn (RoundCard $card): bool => $card->dealt_to_position === $position && $card->passed_to_position !== null,
        );
    }

    /**
     * Raw Points taken so far by each Seat position in this Round.
     *
     * @return array<int, int>
     */
    public function pointsByPosition(): array
    {
        $points = [0 => 0, 1 => 0, 2 => 0, 3 => 0];

        foreach ($this->tricks as $trick) {
            if ($trick->winner_position === null) {
                continue;
            }

            foreach ($this->cards->where('trick_id', $trick->id) as $card) {
                $points[$trick->winner_position] += $card->toCard()->points();
            }
        }

        return $points;
    }
}

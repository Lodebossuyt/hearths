<?php

namespace App\Models;

use App\Hearts\RoomStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * A Room holds four Seats and exactly one Game; its status is the Game's status.
 *
 * @property int $id
 * @property string $code
 * @property RoomStatus $status
 * @property int|null $abandoned_by_user_id
 * @property Carbon|null $started_at
 * @property Carbon|null $ended_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Seat> $seats
 * @property-read Collection<int, Round> $rounds
 * @property-read Round|null $currentRound
 * @property-read User|null $abandonedBy
 */
#[Fillable(['code', 'status', 'abandoned_by_user_id', 'started_at', 'ended_at'])]
class Room extends Model
{
    public const int SEAT_COUNT = 4;

    private const string CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = collect(range(1, 5))
                ->map(fn (): string => self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)])
                ->implode('');
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    /**
     * @return HasMany<Seat, $this>
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class)->orderBy('position');
    }

    /**
     * @return HasMany<Round, $this>
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class)->orderBy('number');
    }

    /**
     * @return HasOne<Round, $this>
     */
    public function currentRound(): HasOne
    {
        return $this->hasOne(Round::class)->latestOfMany('number');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function abandonedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abandoned_by_user_id');
    }

    /**
     * @param  Builder<Room>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereIn('status', [RoomStatus::Waiting, RoomStatus::Playing]);
    }

    public function seatFor(User $user): ?Seat
    {
        return $this->seats->firstWhere('user_id', $user->id);
    }

    public function isFull(): bool
    {
        return $this->seats->count() >= self::SEAT_COUNT;
    }

    public function lowestFreePosition(): ?int
    {
        $taken = $this->seats->pluck('position')->all();

        foreach (range(0, self::SEAT_COUNT - 1) as $position) {
            if (! in_array($position, $taken, true)) {
                return $position;
            }
        }

        return null;
    }

    public function hasSeated(User $user): bool
    {
        return $this->seatFor($user) !== null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $round_id
 * @property int $number
 * @property int $leader_position
 * @property int|null $winner_position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Round $round
 * @property-read Collection<int, RoundCard> $plays
 */
#[Fillable(['round_id', 'number', 'leader_position', 'winner_position'])]
class Trick extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'leader_position' => 'integer',
            'winner_position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Round, $this>
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /**
     * The cards played to this Trick, in play order.
     *
     * @return HasMany<RoundCard, $this>
     */
    public function plays(): HasMany
    {
        return $this->hasMany(RoundCard::class)->orderBy('play_order');
    }

    public function isComplete(): bool
    {
        return $this->winner_position !== null;
    }
}

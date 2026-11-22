<?php

namespace App\Models;

use App\Hearts\Card;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One card of the deck within a Round, tracking who it was dealt to, who holds it and where it was played.
 *
 * @property int $id
 * @property int $round_id
 * @property string $card
 * @property int $dealt_to_position
 * @property int $held_by_position
 * @property int|null $passed_to_position
 * @property int|null $trick_id
 * @property int|null $play_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Round $round
 * @property-read Trick|null $trick
 */
#[Fillable(['round_id', 'card', 'dealt_to_position', 'held_by_position', 'passed_to_position', 'trick_id', 'play_order'])]
class RoundCard extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dealt_to_position' => 'integer',
            'held_by_position' => 'integer',
            'passed_to_position' => 'integer',
            'trick_id' => 'integer',
            'play_order' => 'integer',
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
     * @return BelongsTo<Trick, $this>
     */
    public function trick(): BelongsTo
    {
        return $this->belongsTo(Trick::class);
    }

    public function toCard(): Card
    {
        return Card::fromCode($this->card);
    }
}

<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kwidoo\CardIssuing\Models\Card;

trait BelongsToCard
{
    /**
     * @return BelongsTo
     */
    public function card(): BelongsTo
    {
        $cardModel = config('card-issuing.card_model', 'Kwidoo\CardIssuing\Models\Card');
        return $this->belongsTo($cardModel, config('card-issuing.card_foreign_key'));
    }

    /**
     * @param Builder $query
     * @param Card $cardModel
     *
     * @return void
     */
    public function scopeByCard(Builder $query, $cardModel): void
    {
        $query->where(config('card-issuing.card_foreign_key'), $cardModel->id);
    }

    /**
     * @param Builder $query
     * @param int $id
     *
     * @return void
     */
    public function scopeByCardId(Builder $query, int $id): void
    {
        $query->where(config('card-issuing.card_foreign_key'), $id);
    }
}

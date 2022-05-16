<?php

namespace Kwidoo\CardIssuing\Traits;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToUser
{
    /**
     * @return BelongsTo
     */
    public function card_holder(): BelongsTo
    {
        $cardHolder = config('card-issuing.card_holder_model', 'App\User');
        return $this->belongsTo($cardHolder, config('card-issuing.card_holder_foreign_key'));
    }

    /**
     * @param Builder $query
     * @param User $order
     *
     * @return void
     */
    public function scopeByUser(Builder $query, $cardHolder): void
    {
        $query->where(config('card-issuing.card_holder_foreign_key'), $cardHolder->id);
    }

    /**
     * @param Builder $query
     * @param int $id
     *
     * @return void
     */
    public function scopeByUserId(Builder $query, int $id): void
    {
        $query->where(config('card-issuing.card_holder_foreign_key'), $id);
    }
}

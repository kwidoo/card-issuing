<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kwidoo\CardIssuing\Models\Card;

trait HasCards
{
    /**
     * @return HasMany
     */
    public function cards(): HasMany
    {
        $cardModel = config('card-issuing.card_model', 'Kwidoo\CardIssuing\Models\Card');
        return $this->hasMany($cardModel, config('card-issuing.card_holder_foreign_key'));
    }

    /**
     * @param Builder $query
     * @param Card $cardModel
     *
     * @return void
     */
    public function scopeByCard(Builder $query, $cardModel): void
    {
        $query->whereHas('cards', function (Builder $query) use ($cardModel) {
            $query->where(implode('.', [
                config('card-issuing.card_model'),
                config('card-issuing.card_foreign_key')
            ]), $cardModel->id);
        });
    }

    /**
     * @param Builder $query
     * @param int $id
     *
     * @return void
     */
    public function scopeByCardId(Builder $query, int $id): void
    {
        $query->whereHas('cards', function (Builder $query) use ($id) {
            $query->where(implode('.', [
                config('card-issuing.card_model'),
                config('card-issuing.card_foreign_key')
            ]), $id);
        });
    }
}

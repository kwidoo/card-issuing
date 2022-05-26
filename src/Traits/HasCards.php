<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kwidoo\CardIssuing\Contracts\Card;
use Kwidoo\CardIssuing\Services\CardFactory;

trait HasCards
{
    /**
     * @return HasMany
     */
    public function cards(): HasMany
    {
        /** @var string */
        $cardModel = config('card-issuing.card_model', 'Kwidoo\CardIssuing\Models\Card');
        return $this->hasMany($cardModel, config('card-issuing.cardholder_foreign_key'));
    }

    /**
     * @param Builder $query
     * @param \Kwidoo\CardIssuing\Contracts\Card $cardModel
     *
     * @return void
     */
    public function scopeByCard(Builder $query, Card $cardModel): void
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

    /**
     * @param Builder $query
     *
     * @return void
     */
    public function activeCards()
    {
        return $this->cards()->isActive();
    }

    /**
     * @param Builder $query
     *
     * @return void
     */
    public function inactiveCards()
    {
        return $this->cards()->isInactive();
    }

    /**
     * @return array<\Kwidoo\CardIssuing\Contracts\Card>
     */
    public function syncCardsWithStripe(): array
    {
        $cards = [];
        foreach ($this->stripe()->issuing->cards->all([
            'cardholder' => $this->cardholder_id
        ])->data as $stripeCard) {
            /** @var string */
            $cardModel = config('card-issuing.card_model', 'Kwidoo\CardIssuing\Models\Card');
            /** @var array<\Kwidoo\CardIssuing\Contracts\Card> $cards */
            $cards[] = $cardModel::newFromStripe($stripeCard);
        }
        return $cards;
    }

    public function getCardsAttribute()
    {
        return new CardFactory($this->cards());
    }
}

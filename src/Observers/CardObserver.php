<?php

namespace Kwidoo\CardIssuing\Observers;

use Kwidoo\CardIssuing\Contracts\Card as ContractsCard;
use Kwidoo\CardIssuing\Models\Card;
use Laravel\Cashier\Cashier;

class CardObserver
{
    /**
     * Handle the Card "creating" event.
     *
     * @param \Kwidoo\CardIssuing\Contracts\Card $card
     *
     * @return \Kwidoo\CardIssuing\Contracts\Card
     */
    public function creating(ContractsCard $card): ContractsCard
    {
        if (!isset($card->currency)) {
            $card->currency = config('card-issuing.card_default_currency', 'USD');
        }
        if (!isset($card->type)) {
            $cardModel = config('card-issuing.card_model', 'Kwidoo\CardIssuing\Models\Card');
            $card->type = $cardModel::TYPE_VIRTUAL;
        }
        return $card;
    }

    /**
     * Handle the Card "created" event.
     *
     * @param \Kwidoo\CardIssuing\Contracts\Card  $card
     * @return void
     *
     * @todo Shipping address for Physical cards
     */
    public function created(ContractsCard $card): ContractsCard
    {
        if ($card->stripe_card_id === null) {
            $columnName = config('card-issuing.cardholder_column_name', 'cardholder_id');
            $stripeCard = Cashier::stripe()->issuing->cards->create([
                'cardholder' => $card->cardholder->$columnName,
                'currency' => $card->currency ?? config('card-issuing.card_default_currency', 'USD'),
                'type' => $card->type,
            ]);
            if (isset($stripeCard->id)) {
                /** @var string */
                $cardModel = config('card-issuing.card_model', 'Kwidoo\CardIssuing\Models\Card');
                $card->update(['stripe_card_id' => $stripeCard->id]);
                /** @var \Kwidoo\CardIssuing\Contracts\Card */
                $card = $cardModel::newFromStripe($stripeCard);
            }
        }
        return $card;
    }
}

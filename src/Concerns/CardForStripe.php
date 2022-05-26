<?php

namespace Kwidoo\CardIssuing\Concerns;

use Kwidoo\CardIssuing\Contracts\Card;
use Laravel\Cashier\Cashier;
use Stripe\Issuing\Card as IssuingCard;

trait CardForStripe
{
    /**
     * @param \Stripe\Issuing\Card $stripeCard
     *
     * @return \Kwidoo\CardIssuing\Contracts\Card
     */
    public static function newFromStripe(IssuingCard $stripeCard): Card
    {
        /** @var string */
        $model = Cashier::$customerModel;
        /** @var \Kwidoo\CardIssuing\Contracts\Cardholder */
        $cardholder = $model::byCardholder($stripeCard->cardholder)->first();
        $id = $stripeCard->id;
        unset($stripeCard->id);
        return static::updateOrCreate([
            'stripe_card_id' => $id,
        ], $stripeCard->toArray() + ['user_id' => $cardholder->id]);
    }

    /**
     * @return \Stripe\Issuing\Card
     */
    public function asStripeCard(): IssuingCard
    {
        return $this->stripe()->issuing->cards->retrieve($this->stripe_card_id);
    }

    /**
     * @param array $options
     *
     * @return \Stripe\Issuing\Card
     */
    public function updateStripeCard($options = []): IssuingCard
    {
        return $this->stripe()->issuing->cards->update($this->stripe_card_id, $options);
    }
}

<?php

namespace Kwidoo\CardIssuing\Contracts;

use Stripe\Issuing\Card as IssuingCard;

interface Card
{
    public static function newFromStripe(IssuingCard $stripeCard): self;
    public function update(array $attributes = [], array $options = []);
}

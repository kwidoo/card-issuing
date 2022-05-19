<?php

namespace Kwidoo\CardIssuing\Contracts;

use Stripe\Issuing\Cardholder as IssuingCardholder;

interface Cardholder
{
    public function createAsStripeCardholder(array $options = []): IssuingCardholder;
    public function update(array $attributes = [], array $options = []);
}

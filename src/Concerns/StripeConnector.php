<?php

namespace Kwidoo\CardIssuing\Concerns;

use Laravel\Cashier\Cashier;

trait StripeConnector
{
    /**
     * Get the Stripe SDK client.
     *
     * @param  array  $options
     * @return \Stripe\StripeClient
     */
    public static function stripe(array $options = [])
    {
        return Cashier::stripe($options);
    }
}

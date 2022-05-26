<?php

namespace Kwidoo\CardIssuing\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as ControllersWebhookController;

class WebhookController extends ControllersWebhookController
{
    /**
     * Handle customer subscription created.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
    }
}

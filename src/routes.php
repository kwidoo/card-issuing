<?php

use Illuminate\Support\Facades\Route;

Route::post('/card-issuing/webhook', [
    'middleware' => \Laravel\Cashier\Http\Middleware\VerifyWebhookSignature::class,
    'uses' => \Kwidoo\CardIssuing\Http\Controllers\WebhookController::class . '@handleWebhook',
    'as' => 'card-issuing.webhook'
]);

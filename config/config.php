<?php

/*
 * You can place your custom package configuration in here.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Card Holder
    |--------------------------------------------------------------------------
    |
    | This setting defines which model will be used to store the card holder
    */
    'cardholder_model' => 'App\Models\User',

    /*
    |--------------------------------------------------------------------------
    | Stripe Card Holder ID
    |--------------------------------------------------------------------------
    |
    | This setting defines which column to store Stripe Card Holder Id
    */
    'cardholder_column' => 'cardholder_id',

    /*
    |--------------------------------------------------------------------------
    | Stripe Card Model
    |--------------------------------------------------------------------------
    |
    | This setting defines which model will be used to store the cards
    */
    'card_model' => 'Kwidoo\CardIssuing\Models\Card',

    /*
    |--------------------------------------------------------------------------
    | Stripe Card Holder Foreign Key
    |--------------------------------------------------------------------------
    |
    | This setting defines which model will be used to store the card holder
    */
    'cardholder_foreign_key' => 'user_id',

    /*
    |--------------------------------------------------------------------------
    | Stripe Transaction Model
    |--------------------------------------------------------------------------
    |
    | This setting defines which model will be used to card transactions
    */
    'transaction_model' => 'Kwidoo\CardIssuing\Models\Transaction',

    /*
    |--------------------------------------------------------------------------
    | Stripe Card Foreign Key
    |--------------------------------------------------------------------------
    |
    | This setting defines which column will be used to store the transactions card
    */
    'card_foreign_key' => 'card_id',

    'card_default_currency' => 'gbp',

];

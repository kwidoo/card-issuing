<?php

namespace Kwidoo\CardIssuing\Contracts;

use Stripe\Issuing\Card as IssuingCard;

interface Card
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_CANCELED_STOLEN = 'stolen';
    public const STATUS_CANCELED_LOST = 'lost';

    public const TYPE_PHYSICAL = 'physical';
    public const TYPE_VIRTUAL = 'virtual';

    public const STATUS_REPLACED_LOST = 'lost';
    public const STATUS_REPLACED_STOLEN = 'stolen';
    public const STATUS_REPLACED_DAMAGED = 'damaged';
    public const STATUS_REPLACED_EXPIRED = 'expired';

    public const SHIPPING_SERVICE_STANDARD = 'standard';
    public const SHIPPING_SERVICE_EXPRESS = 'express';
    public const SHIPPING_SERVICE_PRIORITY = 'priority';

    public const SHIPPING_TYPE_INDIVIDUAL = 'individual';
    public const SHIPPING_TYPE_BULK = 'bulk';

    public const STATUS_MAP = [
        'status' => 'status',
        'status_canceled' => 'cancellation_reason',
        'status_replaced' => 'replacement_reason',
    ];

    /**
     * @param \Stripe\Issuing\Card $stripeCard
     *
     * @return self
     */
    public static function newFromStripe(IssuingCard $stripeCard): self;

    /**
     * @param array $options
     *
     * @return \Stripe\StripeClient
     */
    public static function stripe(array $options = []);

    /**
     * @param array $attributes
     * @param array $options
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $attributes = [], array $options = []);
}

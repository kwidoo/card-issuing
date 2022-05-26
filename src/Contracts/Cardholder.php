<?php

namespace Kwidoo\CardIssuing\Contracts;

use Stripe\Issuing\Cardholder as IssuingCardholder;

interface Cardholder
{
    public const ACCOUNT_TYPE_INDIVIDUAL = 'individual';
    public const ACCOUNT_TYPE_COMPANY = 'company';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_BLOCKED = 'blocked';

    public const SPENDING_LIMIT_AUTH = 'per_authorization';
    public const SPENDING_LIMIT_DAILY = 'daily';
    public const SPENDING_LIMIT_WEEKLY = 'weekly';
    public const SPENDING_LIMIT_MONTHLY = 'monthly';
    public const SPENDING_LIMIT_YEARLY = 'yearly';
    public const SPENDING_LIMIT_ALL_TIME = 'all_time';

    public function createAsStripeCardholder(array $options = []): IssuingCardholder;
    public function asStripeCardHolder(): ?IssuingCardholder;
    public function createOrGetStripeCardHolder($options = []): IssuingCardholder;
    public function updateStripeCardHolder($options): IssuingCardholder;
    public function hasCardholderId(): bool;

    public function update(array $attributes = [], array $options = []);
}

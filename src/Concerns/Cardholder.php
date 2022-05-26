<?php

namespace Kwidoo\CardIssuing\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Kwidoo\CardIssuing\Exceptions\CardHolderAlreadyExists;
use Kwidoo\CardIssuing\Exceptions\CardholderDataValidation;
use Kwidoo\CardIssuing\Traits\ClassConstants;
use Kwidoo\CardIssuing\Traits\HasCards;
use Kwidoo\CardIssuing\Traits\SpendingControl;
use Stripe\Issuing\Cardholder as IssuingCardholder;

trait Cardholder
{
    use ClassConstants;
    use HasCards;
    use SpendingControl;

    /**
     * @param array $options
     *
     * @return \Stripe\Issuing\Cardholder
     */
    public function createAsStripeCardholder(array $options = []): IssuingCardholder
    {
        if ($this->hasCardholderId()) {
            throw CardHolderAlreadyExists::exists($this);
        }

        if (!array_key_exists('email', $options)) {
            $options['email'] = $this->email;
        }
        if (!array_key_exists('name', $options)) {
            $options['name'] = $this->name;
        }

        if ($this->withValidation()) {
            $validator = Validator::make($options, $this->getCardholderCreateRules());
            if ($validator->fails()) {
                throw new CardholderDataValidation($validator->errors()->first());
            }
        }

        if (!array_key_exists('status', $options)) {
            $options['status'] = constant('self::STATUS_ACTIVE');
        }

        if (!array_key_exists('type', $options)) {
            $options['type'] = constant('self::ACCOUNT_TYPE_INDIVIDUAL');
        }

        /** @var string */
        $columnName = config('card-issuing.cardholder_column', 'cardholder_id');
        /** @var \Stripe\Issuing\Cardholder */
        $cardholder = $this->stripe()->issuing->cardholders->create($options);
        if (isset($cardholder->id)) {
            $this->$columnName = $cardholder->id;
            $this->save();
        }
        return $cardholder;
    }

    /**
     * @return \Stripe\Issuing\Cardholder
     */
    public function asStripeCardHolder(): ?IssuingCardholder
    {
        /** @var string */
        $columnName = config('card-issuing.cardholder_column', 'cardholder_id');

        if ($this->hasCardholderId()) {
            return $this->stripe()->issuing->cardholders->retrieve($this->$columnName);
        }
        return null;
    }

    /**
     * @param array $options
     *
     * @return \Stripe\Issuing\Cardholder
     */
    public function createOrGetStripeCardHolder($options = []): IssuingCardholder
    {
        return $this->hasCardholderId() ? $this->asStripeCardHolder() : $this->createAsStripeCardHolder($options);
    }

    /**
     * @param mixed $options
     *
     * @return \Stripe\Issuing\Cardholder
     */
    public function updateStripeCardHolder($options): IssuingCardholder
    {
        /** @var string */
        $columnName = config('card-issuing.cardholder_column');

        if ($this->withValidation()) {
            $validator = Validator::make($options, $this->getCardholderUpdateRules());
            if ($validator->fails()) {
                throw new CardholderDataValidation($validator->errors()->first());
            }
        }

        return $this->stripe()->issuing->cardholders->update(
            $this->$columnName,
            $options
        );
    }

    /**
     * Check if Card Holder already exists;
     *
     * @return bool
     */
    public function hasCardholderId(): bool
    {
        /** @var string */
        $columnName = config('card-issuing.cardholder_column');
        return $this->$columnName !== null;
    }

    /**
     * @param Builder $query
     * @param \Stripe\Issuing\Cardholder $cardholder
     *
     * @return void
     */
    public function scopeByCardholder(Builder $query, IssuingCardholder $cardholder): void
    {
        /** @var string */
        $columnName = config('card-issuing.cardholder_column');
        $query->where($columnName, $cardholder->id);
    }

    /**
     * @param Builder $query
     * @param int $id
     *
     * @return void
     */
    public function scopeByCardholderId(Builder $query, int $id): void
    {
        /** @var string */
        $columnName = config('card-issuing.cardholder_column');
        $query->where($columnName, $id);
    }

    /**
     * Should validation be skipped
     *
     * @return bool
     */
    protected function withValidation(): bool
    {
        return $this->stripeValidation ?? true;
    }

    /**
     * Validation rules to validate Cardholder creation data
     *
     * @return array
     */
    protected function getCardholderCreateRules(): array
    {
        if (is_array($this->cardIssueValidationRules) && array_key_exists('cardholder_create', $this->cardIssueValidationRules)) {
            return $this->cardIssueValidationRules['cardholder_create'];
        }
        $accountType = $this->getClassConstants('ACCOUNT_TYPE');
        $status = $this->getClassConstants('STATUS');
        return [
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => ['required', Rule::phone()->country(config('card-issuing.allowed_countries'))],
            'billing' => 'required|array',
            'billing.address' => 'required|array',
            'billing.address.line1' => 'required|string',
            'billing.address.city' => 'required|string',
            'billing.address.state' => 'sometimes|string',
            'billing.address.postal_code' => 'required|string',
            'billing.address.country' => ['required', 'string', Rule::in(config('card-issuing.allowed_countries'))],
            'type' => 'nullable|in:i' . $accountType->implode(','),
            'status' => 'nullable|in:a' . $status->implode(','),
        ];
    }

    /**
     * @return array
     */
    protected function getCardholderUpdateRules(): array
    {
        if (is_array($this->cardIssueValidationRules) && array_key_exists('cardholder_update', $this->cardIssueValidationRules)) {
            return $this->cardIssueValidationRules['cardholder_update'];
        }
        return []; //@todo add update validation rules
    }
}

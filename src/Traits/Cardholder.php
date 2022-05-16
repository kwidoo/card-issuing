<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Kwidoo\CardIssuing\Exceptions\CardHolderAlreadyExists;
use Kwidoo\CardIssuing\Exceptions\CardholderDataValidation;
use Stripe\Issuing\Cardholder as IssuingCardholder;

trait Cardholder
{
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

        $validator = Validator::make($options, $this->getCreateValidationRules());
        if ($validator->fails()) {
            throw new CardholderDataValidation($validator->errors()->first());
        }

        if (!array_key_exists('status', $options)) {
            $options['status'] = 'active';
        }

        if (!array_key_exists('type', $options)) {
            $options['type'] = 'individual';
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
     * Validation rules to validate Cardholder creation data
     *
     * @return array
     */
    public function getCreateValidationRules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'billing' => 'required|array',
            'billing.address' => 'required|array',
            'billing.address.line1' => 'required|string',
            'billing.address.city' => 'required|string',
            'billing.address.state' => 'sometimes|string',
            'billing.address.postal_code' => 'required|string',
            'billing.address.country' => 'required|string',
            'type' => 'nullable|in:individual,company',
        ];
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
}

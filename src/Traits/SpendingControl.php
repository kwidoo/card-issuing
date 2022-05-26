<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Kwidoo\CardIssuing\Exceptions\SpendingLimitDataValidation;
use Stripe\Issuing\Cardholder;

trait SpendingControl
{
    /**
     * @param string|string[] $categories
     *
     * @return \Stripe\Issuing\Cardholder
     */
    public function updateAllowedCategories($categories = []): Cardholder
    {
        $data = ['spending_controls' => ['allowed_categories' => Arr::wrap($categories)]];
        if ($this->withValidation()) {
            $validator = Validator::make($data, $this->getSpendingLimitCategoriesRules());
            if ($validator->fails()) {
                throw new SpendingLimitDataValidation($validator->errors()->first());
            }
        }
        return $this->stripe()->issuing->cardholders->update(
            $this->cardholder_id,
            $data
        );
    }


    /**
     * @param string|string[] $categories
     *
     * @return \Stripe\Issuing\Cardholder
     */
    public function updateBlockedCategories($categories = []): Cardholder
    {
        $data = ['spending_controls' => ['blocked_categories' => Arr::wrap($categories)]];
        if ($this->withValidation()) {
            $validator = Validator::make($data, $this->getSpendingLimitCategoriesRules());
            if ($validator->fails()) {
                throw new SpendingLimitDataValidation($validator->errors()->first());
            }
        }
        return $this->stripe()->issuing->cardholders->update(
            $this->cardholder_id,
            $data
        );
    }

    /**
     * @param int $amount
     * @param string $interval
     * @param string|string[] $categories
     *
     * @return \Stripe\Issuing\Cardholder
     */
    public function updateSpendingLimits(int $amount, string $interval, $categories = []): Cardholder
    {
        /** Global spending limits */
        $data = [
            'spending_controls' => [
                'spending_limits' => [[
                    'amount' => $amount,
                    'interval' => $interval
                ]]
            ]
        ];
        $categories = Arr::wrap($categories);
        if ($categories !== []) {
            $data['spending_controls']['spending_limits'][0]['categories'] = $categories;
        }
        if ($this->withValidation()) {
            $validator = Validator::make($data, $this->getSpendingLimitRules());
            if ($validator->fails()) {
                throw new SpendingLimitDataValidation($validator->errors()->first());
            }
        }
        return $this->stripe()->issuing->cardholders->update(
            $this->cardholder_id,
            $data
        );
    }

    /**
     * Spending limits validation rules
     *
     * @return string[]
     */
    protected function getSpendingLimitRules(): array
    {
        if (is_array($this->cardIssueValidationRules) && array_key_exists('spending_limit_update', $this->cardIssueValidationRules)) {
            return $this->cardIssueValidationRules['spending_limit_update'];
        }
        $constants = $this->getClassConstants('SPENDING_LIMIT');
        return [
            'spending_controls' => 'required|array',
            'spending_controls.spending_limits' => 'required|array',
            'spending_controls.spending_limits.0.amount' => 'required|integer',
            'spending_controls.spending_limits.0.interval' => 'required|string|in:' . $constants->implode(','),
            'spending_controls.spending_limits.0.categories' => 'nullable|array',
        ];
    }

    /**
     * Spending limit allowed/blocked categories rules
     *
     * @return array
     */
    protected function getSpendingLimitCategoriesRules(): array
    {
        if (is_array($this->cardIssueValidationRules) && array_key_exists('spending_limit_categories', $this->cardIssueValidationRules)) {
            return $this->cardIssueValidationRules['spending_limit_categories'];
        }
        return [
            'spending_controls' => 'required|array',
            'spending_controls.blocked_categories' => 'required_without:spending_controls.allowed_categories|array',
            'spending_controls.allowed_categories' => 'required_without:spending_controls.blocked_categories|array',
            'spending_controls.allowed_categories.*' => 'string', //@todo add available categories
            'spending_controls.blocked_categories.*' => 'string', //@todo add available categories
        ];
    }
}

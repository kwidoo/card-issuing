<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kwidoo\CardIssuing\Models\Transaction;

trait HasTransactions
{
    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        $transactionModel = config('card-issuing.transaction_model', 'Kwidoo\CardIssuing\Models\Transaction');
        return $this->hasMany($transactionModel, config('card-issuing.transaction_foreign_key'));
    }

    /**
     * @param Builder $query
     * @param Transaction $transactionModel
     *
     * @return void
     */
    public function scopeByTransaction(Builder $query, $transactionModel): void
    {
        $query->whereHas('transactions', function (Builder $query) use ($transactionModel) {
            $query->where(implode('.', [
                config('card-issuing.transaction_model'),
                config('card-issuing.card_foreign_key')
            ]), $transactionModel->id);
        });
    }

    /**
     * @param Builder $query
     * @param int $id
     *
     * @return void
     */
    public function scopeByCardId(Builder $query, int $id): void
    {
        $query->whereHas('transactions', function (Builder $query) use ($id) {
            $query->where(implode('.', [
                config('card-issuing.transaction_model'),
                config('card-issuing.card_foreign_key')
            ]), $id);
        });
    }
}

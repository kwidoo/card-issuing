<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kwidoo\CardIssuing\Contracts\Cardholder;

trait BelongsToCardholder
{
    /**
     * @return BelongsTo
     */
    public function cardholder(): BelongsTo
    {
        $cardholder = config('card-issuing.cardholder_model', 'App\Models\User');
        return $this->belongsTo($cardholder, config('card-issuing.cardholder_foreign_key', constant('self::CARDHOLDER_FOREIGN_KEY')));
    }

    /**
     * @param Builder $query
     * @param Cardholder $cardholder
     *
     * @return void
     */
    public function scopeByCardholder(Builder $query, Cardholder $cardholder): void
    {
        $query->where(config('card-issuing.cardholder_foreign_key', constant('self::CARDHOLDER_FOREIGN_KEY')), $cardholder->id);
    }

    /**
     * @param Builder $query
     * @param int $id
     *
     * @return void
     */
    public function scopeByCardholderId(Builder $query, int $id): void
    {
        $query->where(config('card-issuing.cardholder_foreign_key', constant('self::CARDHOLDER_FOREIGN_KEY')), $id);
    }
}

<?php

namespace Kwidoo\CardIssuing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\CardIssuing\Collections\TransactionCollection;
use Kwidoo\CardIssuing\Contracts\Transaction as ContractsTransaction;
use Kwidoo\CardIssuing\Traits\BelongsToCard;

class Transaction extends Model implements ContractsTransaction
{
    use HasFactory;
    use BelongsToCard;

    /**
     * @param array $models
     *
     * @return TransactionCollection<\Kwidoo\CardIssuing\Contracts\Transaction>
     */
    public function newCollection(array $models = [])
    {
        return new TransactionCollection($models);
    }
}

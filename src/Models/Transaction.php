<?php

namespace Kwidoo\CardIssuing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\CardIssuing\Concerns\TransactionCollection;
use Kwidoo\CardIssuing\Contracts\Transaction as iTransaction;
use Kwidoo\CardIssuing\Traits\BelongsToCard;

class Transaction extends Model implements iTransaction
{
    use HasFactory;
    use BelongsToCard;
    use TransactionCollection;
}

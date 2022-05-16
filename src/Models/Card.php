<?php

namespace Kwidoo\CardIssuing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\CardIssuing\Traits\BelongsToUser;
use Kwidoo\CardIssuing\Traits\HasTransactions;

class Card extends Model
{
    use HasFactory;
    use BelongsToUser;
    use HasTransactions;
}

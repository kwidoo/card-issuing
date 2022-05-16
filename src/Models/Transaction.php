<?php

namespace Kwidoo\CardIssuing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\CardIssuing\Traits\BelongsToCard;

class Transaction extends Model
{
    use HasFactory;
    use BelongsToCard;
}

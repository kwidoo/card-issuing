<?php

namespace Kwidoo\CardIssuing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\CardIssuing\Traits\BelongsToCard;

class Authorization extends Model
{
    use HasFactory;
    use BelongsToCard;
}

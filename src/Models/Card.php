<?php

namespace Kwidoo\CardIssuing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\CardIssuing\Concerns\CardCollection;
use Kwidoo\CardIssuing\Concerns\CardForStripe;
use Kwidoo\CardIssuing\Concerns\StripeConnector;
use Kwidoo\CardIssuing\Contracts\Card as iCard;
use Kwidoo\CardIssuing\Traits\BelongsToCardholder;
use Kwidoo\CardIssuing\Traits\HasTransactions;
use Kwidoo\CardIssuing\Traits\StatusTrait;

class Card extends Model implements iCard
{
    use HasFactory;
    use StatusTrait;
    //
    use CardCollection;
    use CardForStripe;
    use StripeConnector;
    use BelongsToCardholder;

    use HasTransactions;
    /**
     * The attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'shipping' => 'json', //@todo make cast class, implement with contacts, same for billing address
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'stripe_card_id',
        'brand',
        'cancellation_reason',
        'status',
        'type',
        'currency',
        'last4',
        'exp_month',
        'exp_year',
        'shipping',
    ];
}

<?php

namespace Kwidoo\CardIssuing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\CardIssuing\Collections\CardCollection;
use Kwidoo\CardIssuing\Contracts\Card as ContractsCard;
use Kwidoo\CardIssuing\Traits\BelongsToCardholder;
use Kwidoo\CardIssuing\Traits\HasTransactions;
use Kwidoo\CardIssuing\Traits\StatusTrait;
use Laravel\Cashier\Cashier;
use Stripe\Issuing\Card as IssuingCard;

class Card extends Model implements ContractsCard
{
    use HasFactory;
    use BelongsToCardholder;
    use HasTransactions;
    use StatusTrait;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_CANCELED_STOLEN = 'stolen';
    public const STATUS_CANCELED_LOST = 'lost';

    public const TYPE_PHYSICAL = 'physical';
    public const TYPE_VIRTUAL = 'virtual';

    public const STATUS_REPLACED_LOST = 'lost';
    public const STATUS_REPLACED_STOLEN = 'stolen';
    public const STATUS_REPLACED_DAMAGED = 'damaged';
    public const STATUS_REPLACED_EXPIRED = 'expired';

    public const STATUS_MAP = [
        'status' => 'status',
        'status_canceled' => 'cancellation_reason',
        'status_replaced' => 'replacement_reason',
    ];

    protected $fillable = [
        'user_id',
        'stripe_card_id',
        'card_brand',
        'cancellation_reason',
        'status',
        'type',
        'currency',
        'last_four',
        'exp_month',
        'exp_year',
    ];

    /**
     * @param \Stripe\Issuing\Card $stripeCard
     *
     * @return \Kwidoo\CardIssuing\Contracts\Card
     */
    public static function newFromStripe(IssuingCard $stripeCard): ContractsCard
    {
        /** @var string */
        $model = Cashier::$customerModel;
        /** @var \Kwidoo\CardIssuing\Contracts\Cardholder */
        $cardholder = $model::byCardholder($stripeCard->cardholder)->first();
        $id = $stripeCard->id;
        unset($stripeCard->id);

        return static::updateOrCreate([
            'stripe_card_id' => $id,
        ], $stripeCard->toArray() + ['user_id' => $cardholder->id]);
    }

    /**
     * @param array $models
     *
     * @return CardCollection<\Kwidoo\CardIssuing\Contracts\Card>
     */
    public function newCollection(array $models = [])
    {
        return new CardCollection($models);
    }
}

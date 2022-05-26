<?php

namespace Kwidoo\CardIssuing\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kwidoo\CardIssuing\Contracts\Card;
use Kwidoo\CardIssuing\Traits\ClassConstants;

/**
 * Class CardFactory
 * @package Kwidoo\CardIssuing
 *
 * @method self virtual()
 * @method self physical()
 * @method self standard()
 * @method self express()
 * @method self priority()
 * @method self bulk()
 * @method self individual()
 *
 * @method self type()
 * @method self currency()
 *
 */
class CardFactory
{
    use ClassConstants;

    protected string $type;

    protected string $currency;

    protected array $shipping = [];

    protected ?string $shippingService = null;

    protected ?string $shippingType = null;

    protected ?string $name = null;

    /**
     * @var HasMany<Card>
     */
    protected HasMany $cardRelation;

    /**
     * @param HasMany $cardRelation
     */
    public function __construct(HasMany $cardRelation)
    {
        $this->type = Card::TYPE_VIRTUAL;
        $this->currency = config('card-issuing.card_default_currency', 'USD');;
        $this->cardRelation = $cardRelation;
        $this->name = $cardRelation->getParent()->name;
    }

    /**
     * @param array $data
     *
     * @return Card
     */
    public function create($data = []): Model
    {
        return $this->cardRelation->create(array_merge($data, $this->asArray()));
    }

    /**
     * @param string $type
     *
     * @return self
     */
    protected function setType(string $type = Card::TYPE_VIRTUAL): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $currency
     *
     * @return self
     */
    protected function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param array $address
     *
     * @return self
     */
    protected function setShipping(array $address): self
    {
        $this->shipping = $address;
        return $this;
    }

    /**
     * @param string $service
     *
     * @return self
     */
    protected function setShippingService(string $service): self
    {
        $this->shippingService = $service;
        return $this;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    protected function setShippingType(string $type): self
    {
        $this->shippingType = $type;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    protected function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }


    public function __call($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            return call_user_func_array([$this, 'set' . $name], $value);
        }

        $cardTypes = $this->getClassConstants('TYPE', Card::class);
        if ($cardTypes->contains($name)) {
            return $this->setType($name);
        }

        $shippingServices = $this->getClassConstants('SHIPPING_SERVICE', Card::class);
        if ($shippingServices->contains($name)) {
            return $this->setShippingService($name);
        }

        $shippingTypes = $this->getClassConstants('SHIPPING_TYPE', Card::class);
        if ($shippingTypes->contains($name)) {
            return $this->setShippingType($name);
        }
    }

    /**
     * @return array
     */
    protected function asArray(): array
    {
        return array_merge(
            [
                'type' => $this->type,
                'currency' => $this->currency
            ],
            $this->shipping === [] ? [] :
                [
                    'shipping' => [
                        'name' => $this->name,
                        'address' => $this->shipping,
                        'service' => $this->shippingService,
                        'type' => $this->shippingType
                    ]
                ]
        );
    }
}

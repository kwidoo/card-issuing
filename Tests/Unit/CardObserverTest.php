<?php

namespace Kwidoo\CardIssuing\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Kwidoo\CardIssuing\Contracts\Card;
use Kwidoo\CardIssuing\Contracts\Cardholder;
use Kwidoo\CardIssuing\Observers\CardObserver;
use Tests\TestCase;

class CardObserverTest extends TestCase
{
    use WithFaker;

    /**
     * @var Cardholder
     */
    protected Cardholder $billable;

    /**
     * @var string
     */
    protected string $cardModel;

    /**
     * @var Card
     */
    protected Card $card;

    /**
     * @var CardObserver
     */
    protected CardObserver $observer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $cardholderModel = config('card-issuing.cardholder_model', 'App\Models\User');
        $columnName = config('card-issuing.cardholder_column_name', 'cardholder_id');
        $this->cardModel = config('card-issuing.card_model', 'Kwidoo\CardIssuing\Models\Card');

        $this->observer = new CardObserver();

        $this->card = new $this->cardModel();

        $this->billable = $cardholderModel::factory()->create();
        $this->billable->$columnName = 'test';
    }

    /**
     * @return void
     */
    public function testCardObserverCreatingMethodWithoutDefaultParams(): void
    {
        $this->card->user_id = $this->billable->id;
        $this->card = $this->observer->creating($this->card);

        $this->assertEquals(config('card-issuing.card_default_currency', 'USD'), $this->card->currency);
        $this->assertEquals($this->cardModel::TYPE_VIRTUAL, $this->card->type);
    }

    /**
     * @return void
     */
    public function testCardObserverCreatingMethodWithFullParams(): void
    {
        $this->card->user_id = $this->billable->id;
        $this->card->currency = 'RUB';
        $this->card->type = $this->cardModel::TYPE_PHYSICAL;
        $this->card = $this->observer->creating($this->card);

        $this->assertEquals('RUB', $this->card->currency);
        $this->assertEquals($this->cardModel::TYPE_PHYSICAL, $this->card->type);
    }

    /**
     * @return void
     */
    public function testCardObserverCreatedMethodForVirtualCard(): void
    {
        $columnName = config('card-issuing.cardholder_column_name', 'cardholder_id');
        $this->billable->update([$columnName => null]);
        $this->billable->createAsStripeCardholder([
            'phone_number' => $this->faker->e164PhoneNumber(),
            'billing' =>
            [
                'address' => [
                    'line1' => $this->faker->streetAddress(),
                    // 'line2' => '',
                    'city' => $this->faker->city,
                    'state' => $this->faker->state,
                    'postal_code' => $this->faker->postcode,
                    'country' => 'GB',  //change to your country code
                ]
            ]
        ]);
        $this->card->user_id = $this->billable->id;
        $this->card->currency = 'GBP';
        $this->card->type = $this->cardModel::TYPE_VIRTUAL;
        /** @var Card */
        $this->card = $this->observer->created($this->card);

        $this->assertEquals('gbp', $this->card->currency);
        $this->assertEquals($this->cardModel::TYPE_VIRTUAL, $this->card->type);
        $this->assertNotEmpty($this->card->stripe_card_id);
        $this->assertNotEmpty($this->card->last4);
        $this->assertNotEmpty($this->card->brand);
        $this->assertNotEmpty($this->card->exp_month);
        $this->assertNotEmpty($this->card->exp_year);
    }

    /**
     * @return void
     */
    public function testCardObserverCreatedMethodForPhysicalCard(): void
    {
        $this->markTestIncomplete();
        $columnName = config('card-issuing.cardholder_column_name', 'cardholder_id');
        $this->billable->update([$columnName => null]);
        $this->billable->createAsStripeCardholder([
            'phone_number' => $this->faker->e164PhoneNumber(),
            'billing' =>
            [
                'address' => [
                    'line1' => $this->faker->streetAddress(),
                    // 'line2' => '',
                    'city' => $this->faker->city,
                    'state' => $this->faker->state,
                    'postal_code' => $this->faker->postcode,
                    'country' => 'GB',  //change to your country code
                ]
            ], 'shipping' =>
            [
                'address' => [
                    'line1' => $this->faker->streetAddress(),
                    // 'line2' => '',
                    'city' => $this->faker->city,
                    'state' => $this->faker->state,
                    'postal_code' => $this->faker->postcode,
                    'country' => 'GB',  //change to your country code
                ]
            ]
        ]);
        $this->card->user_id = $this->billable->id;
        $this->card->currency = 'GBP';
        $this->card->type = $this->cardModel::TYPE_PHYSICAL;
        /** @var Card */
        $this->card = $this->observer->created($this->card);

        $this->assertEquals('gbp', $this->card->currency);
        $this->assertEquals($this->cardModel::TYPE_PHYSICAL, $this->card->type);
        $this->assertNotEmpty($this->card->stripe_card_id);
        $this->assertNotEmpty($this->card->last4);
        $this->assertNotEmpty($this->card->brand);
        $this->assertNotEmpty($this->card->exp_month);
        $this->assertNotEmpty($this->card->exp_year);
    }
}

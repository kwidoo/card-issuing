<?php

namespace Kwidoo\CardIssuing\Tests\Unit;

use Kwidoo\CardIssuing\Contracts\Cardholder;
use Kwidoo\CardIssuing\Exceptions\CardholderAlreadyExists;
use Tests\TestCase;

class CardholderTraitTest extends TestCase
{
    /**
     * @var Cardholder
     */
    protected Cardholder $billable;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $cardholderModel = config('card-issuing.cardholder_model', 'App\Models\User');

        $this->billable = $cardholderModel::factory()->create();
    }

    public function testCreateAsStripeCardholderWillThrowErrorOnExistingCardholderId()
    {
        $columnName = config('card-issuing.cardholder_column_name', 'cardholder_id');
        $this->billable->$columnName = 'test';

        $cardholderModel = explode('\\', get_class($this->billable));
        $this->expectException(CardholderAlreadyExists::class);
        $this->expectExceptionMessage(end($cardholderModel) . ' is already a Cardholder with with ID test.');
        $this->billable->createAsStripeCardholder();
    }
}

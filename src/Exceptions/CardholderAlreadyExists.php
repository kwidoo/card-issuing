<?php

namespace Kwidoo\CardIssuing\Exceptions;

use Exception;

class CardholderAlreadyExists extends Exception
{
    /**
     * Create a new CustomerAlreadyCreated instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function exists($owner)
    {
        $columnName = config('card-issuing.cardholder_column_name', 'cardholder_id');
        return new static(class_basename($owner) . " is already a Cardholder with with ID {$owner->$columnName}.");
    }
}

<?php

namespace Kwidoo\CardIssuing;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kwidoo\CardIssuing\Skeleton\SkeletonClass
 */
class CardIssuingFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'card-issuing';
    }
}

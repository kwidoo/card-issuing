<?php

namespace Kwidoo\CardIssuing\Concerns;

use Kwidoo\CardIssuing\Collections\CardCollection as CollectionsCardCollection;

trait CardCollection
{
    /**
     * @param array $models
     *
     * @return \Kwidoo\CardIssuing\Collections\CardCollection<\Kwidoo\CardIssuing\Contracts\Card>
     */
    public function newCollection(array $models = [])
    {
        return new CollectionsCardCollection($models);
    }
}

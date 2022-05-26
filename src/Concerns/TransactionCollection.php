<?php

namespace Kwidoo\CardIssuing\Concerns;

use Kwidoo\CardIssuing\Collections\TransactionCollection as CollectionsTransactionCollection;

trait TransactionCollection
{
    /**
     * @param array $models
     *
     * @return \Kwidoo\CardIssuing\Collections\TransactionCollection<\Kwidoo\CardIssuing\Contracts\Transaction>
     */
    public function newCollection(array $models = [])
    {
        return new TransactionCollection($models);
    }
}

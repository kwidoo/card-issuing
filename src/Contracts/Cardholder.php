<?php

namespace Kwidoo\CardIssuing\Contracts;

interface Cardholder
{
    public function update(array $attributes = [], array $options = []);
}

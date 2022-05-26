<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Trait ClassConstants
 * @package Kwidoo\CardIssuing\Traits
 *
 * @todo move to helpers
 */
trait ClassConstants
{
    /**
     * Provide class constants for specific key names.
     * @param string $key
     *
     * @return Collection<string>
     */
    protected function getClassConstants(string $key, $class = null)
    {
        if ($class === null) {
            $class = $this;
        }
        $classReflection = new ReflectionClass($class);
        return collect($classReflection->getConstants())
            ->filter(function ($value, $constant) use ($key) {
                return Str::startsWith($constant, $key);
            });
    }
}

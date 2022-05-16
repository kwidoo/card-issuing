<?php

namespace Kwidoo\CardIssuing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Usage:
 *
 * 1. Add trait to your model
 * 2. If your model uses other 'status' field/property, add this to your model:
 *      public $statusMap = [
 *          'status' => 'request_status',
 *      ];
 *    this will tell the trait to use 'request_status' field/property instead of 'status'
 * 3. Add constants to your model like this:
 *     const STATUS_PENDING = 'pending';
 *     const STATUS_APPROVED = 'approved';
 *
 *    this will tell to create attributes for model isActive, isPending, isNotActive, isNotPending and respective scopes
 *
 * 2a. If you would like to have more than one status field in your model, add this to your model:
 *      public $statusMap = [
 *          'status' => 'order_status',
 *          'status_delivery' => 'delivery_status',
 *      ];
 *
 *     this will tell the trait to use 'order_status' field/property instead of 'status' and 'delivery_status' instead of 'status_delivery'
 * 3a. Add constants to your model like this:
 *
 *     for status field/property
 *     const STATUS_PENDING = 'pending';
 *     const STATUS_APPROVED = 'approved';
 *
 *     for status_delivery field/property
 *     const STATUS_DELIVERY_PENDING = 'pending';
 *     const STATUS_DELIVERY_APPROVED = 'approved';
 *
 * 4. Use in code:
 *       $model->isActive;
 *       $model->isPending;
 *       Model::isActive();
 *       Model::isPending();
 *
 *     @todo: currently only first two words are used for status field and remaining for value. You can't use STATUS_IN_PROGRESS, use STATUS_INPROGRESS instead.
 *     @todo add status combination:
 *
 */
trait StatusTrait
{
    public function __get($name)
    {
        return $this->addStatusAttributes($name) ?? parent::__get($name);
    }

    /**
     * Append the status attributes to the model.
     * Example: if $model->isActive is called, it will return $model->status === 'active'
     *
     * @param mixed $name
     *
     * @return bool|void
     */
    protected function addStatusAttributes($name)
    {
        if (!method_exists($this, 'get' . ucfirst($name) . 'Attribute') && !property_exists($this, ucfirst($name))) {
            list($mapKey, $prefix) = $this->parsePrefix($name);
            $key = $this->getStatusKey($mapKey);
            if (array_key_exists($key, $this->getStatusMap())) {
                $field = $this->getStatusMap()[$key];
                $constant =  $this->prepareConstant($mapKey);

                if (defined($constant)) {
                    return Str::endsWith($prefix, ['Not']) && defined($constant) ? constant($constant) !== $this->$field : constant($constant) === $this->$field;
                }
            }
        }
    }

    /**
     * Parse status field name
     * @todo: distinguish database field or eloquent property name from actual status name by $field
     *
     * @param string|null $mapKey
     *
     * @return string
     */
    protected function getStatusKey($mapKey)
    {
        $type = explode('_', Str::snake($mapKey));
        array_pop($type);
        array_unshift($type, 'status');
        return implode('_', $type);
    }

    /**
     * Returns positive or negative prefix and key name.
     * Example: isNotActive will return ['Active', 'isNot']
     *
     * @param string|null $name
     *
     * @return array|void
     */
    protected function parsePrefix($name)
    {
        foreach ($this->getStatusPrefixes() as $prefix) {
            $mapKey = $this->getPositiveKey($prefix, $name);
            if (Str::startsWith($name, [$prefix . 'Not'])) {
                return [$mapKey, $prefix . 'Not'];
            }
            if (Str::startsWith($name, [$prefix])) {
                return [$mapKey, $prefix];
            }
        }
    }

    /**
     * Remove the prefix from the name. Default isActive and isNotActive will return Active
     * For scopes start word scope will be also removed
     *
     * @param string $prefix
     * @param string $name
     *
     * @return string
     */
    protected function getPositiveKey(string $prefix, string $name): string
    {
        return Str::replace(
            'scope',
            '',
            Str::replace(
                $prefix,
                '',
                Str::replace($prefix . 'Not', '', $name)
            )
        );
    }

    /**
     * Return mapping between constants and database. If nothing is defined by default constant should starts with STATUS_ and second art of it will be status value.
     * Example: const STATUS_ACCEPTED = 'accepted' will be mapped to database/eloquent field 'status' = 'accepted'
     * Example: const STATUS_REQUEST_ACCEPTED = 'accepted' will be mapped to database/eloquent field 'request_status' = 'accepted'
     *
     * @return array
     */
    protected function getStatusMap(): array
    {
        if (!property_exists($this, 'statusMap')) {
            if (defined('self::STATUS_MAP')) {
                return constant('self::STATUS_MAP');
            }
            return ['status' => 'status'];
        }
        return $this->statusMap;
    }

    /**
     * Return available prefixes. By default every status should start with 'is' or 'isNot'.
     * Example: $model->isActive will return $model->status === 'active' and $model->isActive()->get() will apply scope to query ->where('status', 'active');
     *
     * @return array
     */
    protected function getStatusPrefixes(): array
    {
        if (!property_exists($this, 'statusPrefixes')) {
            if (defined('self::STATUS_PREFIXES')) {
                return constant('self::STATUS_PREFIXES');
            }
            return ['is'];
        }
        return $this->statusPrefixes;
    }

    /**
     * Determine if the model has a given scope. We give priority to scopes defined in the model. If nothing will be found, status scope will be applied.
     *
     * @param  string  $scope
     * @return bool
     */
    public function hasNamedScope($name): bool
    {
        if (!method_exists($this, 'scope' . ucfirst($name))) {
            list($mapKey) = $this->parsePrefix($name);
            $key = $this->getStatusKey($mapKey);
            if (array_key_exists($key, $this->getStatusMap()) && defined($this->prepareConstant($mapKey))) {
                return true;
            }
        }
        return method_exists($this, 'scope' . ucfirst($name));
    }

    /**
     * Apply the given named scope if possible.
     *
     * @param  string  $scope
     * @param  array  $parameters
     * @return mixed
     */
    public function callNamedScope($scope, array $parameters = [])
    {
        if (!method_exists($this, $scope)) {
            list($mapKey, $prefix) = $this->parsePrefix($scope);
            $key = $this->getStatusKey($mapKey);
            if (array_key_exists($key, $this->getStatusMap())) {
                $constant =  $this->prepareConstant($mapKey);
                $field = $this->getStatusMap()[$key];
                if (defined($constant)) {
                    return Str::endsWith($prefix, ['Not']) ? $this->negativeScope($parameters[0], $field, $constant) : $this->positiveScope($parameters[0], $field, $constant);
                }
            }
        }

        return $this->{'scope' . ucfirst($scope)}(...$parameters);
    }

    /**
     * Apply positive scope
     * Example: if isActive() will apply ->where('status', self::STATUS_ACTIVE);
     *
     * @param mixed $query
     * @param string $field
     * @param string $constant
     *
     * @return Builder
     */
    protected function positiveScope($query, string $field, string $constant)
    {
        return $query->where($field, '=', constant($constant));
    }

    /**
     * Apply negative scope
     * Example: if isNotActive() will apply ->where('status', '!=', self::STATUS_ACTIVE);
     *
     * @param mixed $query
     * @param string $field
     * @param string $constant
     *
     * @return Builder
     */
    protected function negativeScope($query, string $field, string $constant)
    {
        return $query->where($field, '!=', constant($constant));
    }

    /**
     * @param string $mapKey
     *
     * @return string
     */
    protected function prepareConstant(?string $mapKey): ?string
    {
        return $mapKey ? 'self::STATUS_' . Str::upper(Str::snake($mapKey)) : null;
    }
}

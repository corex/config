<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Config\Adapter\AdapterInterface;
use CoRex\Config\Data\TrueFalse;
use CoRex\Config\Data\Value;
use CoRex\Config\Exceptions\AdapterException;
use CoRex\Config\Exceptions\ConfigException;
use CoRex\Config\Exceptions\TypeException;
use CoRex\Config\Key\Key;

class Config implements ConfigInterface
{
    /** @var array<AdapterInterface> */
    private array $adapters = [];

    /**
     * @param array<AdapterInterface> $adapters
     */
    public function __construct(array $adapters)
    {
        if (count($adapters) === 0) {
            throw new AdapterException('No adapters specified.');
        }

        foreach ($adapters as $adapter) {
            if (!is_object($adapter)) {
                throw new AdapterException('Adapter specified is not an adapter.');
            }

            if (!$adapter instanceof AdapterInterface) {
                throw new AdapterException(
                    sprintf(
                        'Adapter "%s" does not implement %s.',
                        get_class($adapter),
                        AdapterInterface::class
                    )
                );
            }

            $adapterHash = $adapter->getIdentifier()->getHash();
            if (array_key_exists($adapterHash, $this->adapters)) {
                throw new AdapterException(
                    sprintf(
                        'Adapter "%s" already loaded.',
                        get_class($adapter)
                    )
                );
            }

            $this->adapters[$adapterHash] = $adapter;
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $configKey): bool
    {
        return $this->getValueObject($configKey)->hasKey();
    }

    /**
     * @inheritDoc
     */
    public function getValueObject(string $configKey): Value
    {
        $key = new Key($configKey);

        foreach ($this->adapters as $adapter) {
            $value = $adapter->getValue($key);
            if ($value->hasKey()) {
                return $value;
            }
        }

        return new Value(null, $key, null);
    }

    /**
     * @inheritDoc
     */
    public function getMixedOrNull(string $configKey): mixed
    {
        $valueObject = $this->getValueObject($configKey);

        return $valueObject->hasKey() ? $valueObject->getValue() : null;
    }

    /**
     * @inheritDoc
     */
    public function getMixed(string $configKey): mixed
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateKeyFound(__FUNCTION__, $valueObject);

        return $valueObject->getValue();
    }

    /**
     * @inheritDoc
     */
    public function getStringOrNull(string $configKey): ?string
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateType(__FUNCTION__, $valueObject, 'string', false);

        $value = $valueObject->getValue();
        assert(is_string($value) || $value === null);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getString(string $configKey): string
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateKeyFound(__FUNCTION__, $valueObject);
        $this->validateType(__FUNCTION__, $valueObject, 'string', true);

        $value = $valueObject->getValue();
        assert(is_string($value));

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getIntOrNull(string $configKey): ?int
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateType(__FUNCTION__, $valueObject, 'integer', false);

        $value = $valueObject->getValue();
        assert(is_int($value) || $value === null);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getInt(string $configKey): int
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateKeyFound(__FUNCTION__, $valueObject);
        $this->validateType(__FUNCTION__, $valueObject, 'integer', true);

        $value = $valueObject->getValue();
        assert(is_int($value));

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getBoolOrNull(string $configKey): ?bool
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateType(__FUNCTION__, $valueObject, 'boolean', false);

        $value = $valueObject->getValue();
        assert(is_bool($value) || $value === null);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getBool(string $configKey): bool
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateKeyFound(__FUNCTION__, $valueObject);
        $this->validateType(__FUNCTION__, $valueObject, 'boolean', true);

        $value = $valueObject->getValue();
        assert(is_bool($value));

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getTranslatedBoolOrNull(string $configKey): ?bool
    {
        $valueObject = $this->getValueObject($configKey);
        $value = $valueObject->getValue();

        // If key not found or the value is null, return early.
        if ($value === null || !$valueObject->hasKey()) {
            return null;
        }

        $translatedValue = TrueFalse::getTranslatedTrueFalse($value);
        if ($translatedValue !== null) {
            return $translatedValue;
        }

        throw new TypeException(
            sprintf(
                'Value for key "%s" has type "%s". Value must be one of %s' .
                ' or null when using "%s()" to get data via adapter "%s".',
                $valueObject->getKey()->getDotNotation(),
                gettype($value),
                implode(', ', TrueFalse::getReadableTrueFalseValues()),
                __FUNCTION__,
                $valueObject->getAdapter()?->getIdentifier()->getClassName()
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getTranslatedBool(string $configKey): bool
    {
        $valueObject = $this->getValueObject($configKey);
        $value = $valueObject->getValue();

        $this->validateKeyFound(__FUNCTION__, $valueObject);

        $translatedValue = TrueFalse::getTranslatedTrueFalse($valueObject->getValue());
        if ($translatedValue !== null) {
            return $translatedValue;
        }

        throw new TypeException(
            sprintf(
                'Value for key "%s" has type "%s". Value must be one of %s' .
                ' when using "%s()" to get data via adapter "%s".',
                $valueObject->getKey()->getDotNotation(),
                strtolower(gettype($value)),
                implode(', ', TrueFalse::getReadableTrueFalseValues()),
                __FUNCTION__,
                $valueObject->getAdapter()?->getIdentifier()->getClassName()
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getDoubleOrNull(string $configKey): ?float
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateType(__FUNCTION__, $valueObject, 'double', false);

        $value = $valueObject->getValue();
        assert(is_float($value) || $value === null);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getDouble(string $configKey): float
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateKeyFound(__FUNCTION__, $valueObject);
        $this->validateType(__FUNCTION__, $valueObject, 'double', true);

        $value = $valueObject->getValue();
        assert(is_float($value));

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getArrayOrNull(string $configKey): ?array
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateType(__FUNCTION__, $valueObject, 'array', false);

        $value = $valueObject->getValue();
        assert(is_array($value) || $value === null);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getArray(string $configKey): array
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateKeyFound(__FUNCTION__, $valueObject);
        $this->validateType(__FUNCTION__, $valueObject, 'array', true);

        $value = $valueObject->getValue();
        assert(is_array($value));

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getListOrNull(string $configKey): ?array
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateType(__FUNCTION__, $valueObject, 'array', false);
        $this->validateArrayIsList(__FUNCTION__, $valueObject, false);

        $value = $valueObject->getValue();
        assert(is_array($value) || $value === null);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getList(string $configKey): array
    {
        $valueObject = $this->getValueObject($configKey);

        $this->validateKeyFound(__FUNCTION__, $valueObject);
        $this->validateType(__FUNCTION__, $valueObject, 'array', true);
        $this->validateArrayIsList(__FUNCTION__, $valueObject, true);

        $value = $valueObject->getValue();
        assert(is_array($value));

        return $value;
    }

    private function validateKeyFound(string $callerFunctionName, Value $valueObject): void
    {
        if ($valueObject->getAdapter() === null) {
            throw new ConfigException(
                sprintf(
                    'Value for key "%s" does not exist when using "%s()".',
                    $valueObject->getKey()->getDotNotation(),
                    $callerFunctionName
                )
            );
        }
    }

    private function validateType(
        string $callerFunctionName,
        Value $valueObject,
        string $requiredType,
        bool $isRequired
    ): void {
        $currentType = strtolower(gettype($valueObject->getValue()));
        if ($currentType !== $requiredType && $valueObject->getAdapter() !== null) {
            throw new TypeException(
                sprintf(
                    implode(
                        '',
                        [
                            'Value for key "%s" has type "%s". Must be "%s"',
                            !$isRequired ? ' or null' : '',
                            ' when using "%s()" to get data via adapter "%s".'
                        ]
                    ),
                    $valueObject->getKey()->getDotNotation(),
                    $currentType,
                    $requiredType,
                    $callerFunctionName,
                    $valueObject->getAdapter()->getIdentifier()->getClassName()
                )
            );
        }
    }

    private function validateArrayIsList(string $callerFunctionName, Value $valueObject, bool $isRequired): void
    {
        $value = $valueObject->getValue();

        if ($valueObject->getAdapter() === null) {
            return;
        }

        if (
            ($isRequired && $value === null)
            || (is_array($value) && !array_is_list($value))
        ) {
            throw new TypeException(
                sprintf(
                    'Value for key "%s" is an array, but not considered a list since its' .
                    ' keys does not consist of consecutive numbers from 0 to count($array) - 1,' .
                    ' when using "%s()" to get data via adapter "%s".',
                    $valueObject->getKey()->getDotNotation(),
                    $callerFunctionName,
                    $valueObject->getAdapter()->getIdentifier()->getClassName()
                )
            );
        }
    }
}
<?php

declare(strict_types=1);

namespace CoRex\Config\Data;

class TrueFalse
{
    public const VALUES_TRUE = ['true', true, 'yes', 'on', 1, '1'];
    public const VALUES_FALSE = ['false', false, 'no', 'off', 0, '0'];

    /**
     * Get translated true/false value. Null of invalid.
     *
     * Values for true : ['true', true, 'yes', 'on', 1, '1'].
     * Values for false : ['false', false, 'no', 'off', 0, '0'].
     *
     * @param mixed $value
     * @return bool|null
     */
    public static function getTranslatedTrueFalse(mixed $value): ?bool
    {
        if (is_string($value)) {
            $value = strtolower($value);
        }

        if (in_array($value, self::VALUES_TRUE, true)) {
            return true;
        }

        if (in_array($value, self::VALUES_FALSE, true)) {
            return false;
        }

        return null;
    }

    /**
     * Get readable true/false values.
     *
     * @return array<int, bool|int|string>
     */
    public static function getReadableTrueFalseValues(): array
    {
        // Convert to readable true/false values.
        $possibleValues = array_merge(self::VALUES_TRUE, self::VALUES_FALSE);
        foreach ($possibleValues as $index => $possibleValue) {
            if (is_string($possibleValue)) {
                $possibleValues[$index] = "'" . $possibleValue . "'";
            }

            if (is_bool($possibleValue)) {
                $possibleValues[$index] = $possibleValue ? 'true' : 'false';
            }

            if (is_int($possibleValue)) {
                $possibleValues[$index] = (string)$possibleValue;
            }
        }

        return $possibleValues;
    }
}
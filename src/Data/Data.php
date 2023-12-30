<?php

declare(strict_types=1);

namespace CoRex\Config\Data;

class Data
{
    public const NO_VALUE_FOUND_CODE = '18d0b950-a3fb-11ee-a5b9-efd58422b485';

    /**
     * Get data from array.
     *
     * @param array<int|string, mixed> $configArray
     * @param array<string> $keyParts
     * @return array<int|string, mixed>|bool|float|int|string|null
     */
    public static function getFromArray(array $configArray, array $keyParts): array|bool|float|int|string|null
    {
        // If only section is specified, return data.
        if (count($keyParts) === 0) {
            return $configArray;
        }

        // Dig down into data.
        foreach ($keyParts as $keyPart) {
            /*
             * If code arrives here, it means that key still wants to dig down.
             * If $data is not an array or key in associative array does not exist,
             * this means key not found.
             */
            if (!is_array($configArray) || !array_key_exists($keyPart, $configArray)) {
                return self::NO_VALUE_FOUND_CODE;
            }

            $configArray = $configArray[$keyPart];
        }

        return $configArray;
    }
}
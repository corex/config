<?php

declare(strict_types=1);

namespace CoRex\Config;

interface ConfigInterface
{
    /**
     * Determine if the given key exists.
     *
     * @param string $configKey
     * @return bool
     */
    public function has(string $configKey): bool;

    /**
     * Get mixed value including null.
     *
     * @param string $configKey
     * @return mixed
     */
    public function getMixed(string $configKey): mixed;

    /**
     * Get string or null.
     *
     * @param string $configKey
     * @return string|null
     */
    public function getStringOrNull(string $configKey): ?string;

    /**
     * Get string.
     *
     * @param string $configKey
     * @return string
     */
    public function getString(string $configKey): string;

    /**
     * Get int or null.
     *
     * @param string $configKey
     * @return int|null
     */
    public function getIntOrNull(string $configKey): ?int;

    /**
     * Get int.
     *
     * @param string $configKey
     * @return int
     */
    public function getInt(string $configKey): int;

    /**
     * Get bool or null.
     *
     * @param string $configKey
     * @return bool|null
     */
    public function getBoolOrNull(string $configKey): ?bool;

    /**
     * Get bool.
     *
     * @param string $configKey
     * @return bool
     */
    public function getBool(string $configKey): bool;

    /**
     * Get translated bool or null.
     *
     * Values for true : ['true', true, 'yes', 'on', 1, '1'].
     * Values for false : ['false', false, 'no', 'off', 0, '0'].
     *
     * @param string $configKey
     * @return bool|null
     */
    public function getTranslatedBoolOrNull(string $configKey): ?bool;

    /**
     * Get translated bool.
     *
     * Values for true : ['true', true, 'yes', 'on', 1, '1'].
     * Values for false : ['false', false, 'no', 'off', 0, '0'].
     *
     * @param string $configKey
     * @return bool
     */
    public function getTranslatedBool(string $configKey): bool;

    /**
     * Get double or null.
     *
     * @param string $configKey
     * @return float|null
     */
    public function getDoubleOrNull(string $configKey): ?float;

    /**
     * Get double.
     *
     * @param string $configKey
     * @return float
     */
    public function getDouble(string $configKey): float;

    /**
     * Get array or null.
     *
     * @param string $configKey
     * @return array<int|string, mixed>|null
     */
    public function getArrayOrNull(string $configKey): ?array;

    /**
     * Get array.
     *
     * @param string $configKey
     * @return array<int|string, mixed>
     */
    public function getArray(string $configKey): array;

    /**
     * Get list (numeric keys array) or null.
     *
     * @param string $configKey
     * @return array<int|string, mixed>|null
     */
    public function getListOrNull(string $configKey): ?array;

    /**
     * Get list (numeric keys array).
     *
     * @param string $configKey
     * @return array<int|string, mixed>
     */
    public function getList(string $configKey): array;
}
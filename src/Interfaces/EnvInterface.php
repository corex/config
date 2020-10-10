<?php

declare(strict_types=1);

namespace CoRex\Config\Interfaces;

use CoRex\Config\Exceptions\EnvironmentException;

interface EnvInterface
{
    /**
     * Get environments.
     *
     * @return string[]
     */
    public static function getEnvironments(): array;

    /**
     * Is supported.
     *
     * @param string $environment
     * @return bool
     */
    public static function isSupported(string $environment): bool;

    /**
     * Get.
     *
     * @param string $key
     * @param mixed $default Default null.
     * @return mixed
     */
    public static function get(string $key, $default = null);

    /**
     * Get string.
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function getString(string $key, string $default = ''): string;

    /**
     * Get int.
     *
     * @param string $key
     * @param int $default Default 0.
     * @return int
     */
    public static function getInt(string $key, int $default = 0): int;

    /**
     * Get bool.
     *
     * @param string $key
     * @param bool $default Default false.
     * @return bool
     */
    public static function getBool(string $key, bool $default = false): bool;

    /**
     * Get app name.
     *
     * @return string|null
     */
    public static function getAppName(): ?string;

    /**
     * Get app environment.
     *
     * @return string|null
     * @throws EnvironmentException
     */
    public static function getAppEnvironment(): ?string;

    /**
     * Get app debug.
     *
     * @return bool
     */
    public static function getAppDebug(): bool;
}
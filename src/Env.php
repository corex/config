<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Config\Exceptions\EnvironmentException;
use CoRex\Config\Helpers\Value;
use CoRex\Config\Interfaces\EnvInterface;

class Env implements EnvInterface
{
    public const LOCAL = 'local';
    public const TESTING = 'testing';
    public const PRODUCTION = 'production';

    /**
     * Get environments.
     *
     * @return string[]
     */
    public static function getEnvironments(): array
    {
        return [self::LOCAL, self::TESTING, self::PRODUCTION];
    }

    /**
     * Is supported.
     *
     * @param string $environment
     * @return bool
     */
    public static function isSupported(string $environment): bool
    {
        return in_array($environment, self::getEnvironments(), true);
    }

    /**
     * Get.
     *
     * @param string $key
     * @param mixed $default Default null.
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value !== false && $value !== null) {
            return $value;
        }

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        return $default;
    }

    /**
     * Get string.
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function getString(string $key, string $default = ''): string
    {
        return (string)self::get($key, $default);
    }

    /**
     * Get int.
     *
     * @param string $key
     * @param int $default Default 0.
     * @return int
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return intval(self::get($key, $default));
    }

    /**
     * Get bool.
     *
     * @param string $key
     * @param bool $default Default false.
     * @return bool
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);

        return Value::isTrue($value);
    }

    /**
     * Get app name.
     *
     * @return string|null
     */
    public static function getAppName(): ?string
    {
        return self::get('APP_NAME');
    }

    /**
     * Get app environment.
     *
     * @return string|null
     * @throws EnvironmentException
     */
    public static function getAppEnvironment(): ?string
    {
        // Get environment from environment variable.
        $environment = self::get('APP_ENV');

        // Validate APP_ENV value.
        if ($environment !== null && !self::isSupported($environment)) {
            throw new EnvironmentException(sprintf('Environment %s not supported.', $environment));
        }

        return $environment ?? self::PRODUCTION;
    }

    /**
     * Get app debug.
     *
     * @return bool
     */
    public static function getAppDebug(): bool
    {
        return Value::isTrue(self::get('APP_DEBUG'));
    }
}
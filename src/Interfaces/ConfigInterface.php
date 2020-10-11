<?php

declare(strict_types=1);

namespace CoRex\Config\Interfaces;

use CoRex\Config\Exceptions\EnvironmentException;

interface ConfigInterface
{
    /**
     * Config constructor.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader);

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     * @return bool
     * @throws EnvironmentException
     */
    public function has(string $key): bool;

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed $default Default null.
     * @return mixed
     * @throws EnvironmentException
     */
    public function get(string $key, $default = null);

    /**
     * Get string.
     *
     * @param string $key
     * @param string $default
     * @return string
     * @throws EnvironmentException
     */
    public function getString(string $key, string $default = ''): string;

    /**
     * Get int.
     *
     * @param string $key
     * @param int $default
     * @return int
     * @throws EnvironmentException
     */
    public function getInt(string $key, int $default = 0): int;

    /**
     * Get bool.
     *
     * @param string $key
     * @param bool $default
     * @return bool
     * @throws EnvironmentException
     */
    public function getBool(string $key, bool $default = false): bool;
}
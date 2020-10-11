<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Config\Exceptions\EnvironmentException;
use CoRex\Config\Helpers\Value;
use CoRex\Config\Interfaces\ConfigInterface;
use CoRex\Config\Interfaces\LoaderInterface;

class Config implements ConfigInterface
{
    /** @var LoaderInterface */
    private $loader;

    /** @var mixed[] */
    private $data = [];

    /**
     * Config constructor.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     * @return bool
     * @throws EnvironmentException
     */
    public function has(string $key): bool
    {
        return $this->getData($key, 'not.found') !== 'not.found';
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed $default Default null.
     * @return mixed
     * @throws EnvironmentException
     */
    public function get(string $key, $default = null)
    {
        return $this->getData($key, $default);
    }

    /**
     * Get string.
     *
     * @param string $key
     * @param string $default
     * @return string
     * @throws EnvironmentException
     */
    public function getString(string $key, string $default = ''): string
    {
        return (string)$this->get($key, $default);
    }

    /**
     * Get int.
     *
     * @param string $key
     * @param int $default
     * @return int
     * @throws EnvironmentException
     */
    public function getInt(string $key, int $default = 0): int
    {
        return intval($this->get($key, $default));
    }

    /**
     * Get bool.
     *
     * @param string $key
     * @param bool $default
     * @return bool
     * @throws EnvironmentException
     */
    public function getBool(string $key, bool $default = false): bool
    {
        return Value::isTrue($this->get($key, $default));
    }

    /**
     * Get data.
     *
     * @param string $key
     * @param null $default
     * @return mixed|mixed[]|null
     * @throws EnvironmentException
     */
    private function getData(string $key, $default = null)
    {
        $keySegments = trim($key) !== '' ? explode('.', $key) : [];
        $section = array_shift($keySegments);

        // If no section is specified, return default value.
        if ($section === null) {
            return $default;
        }

        // Get from environment if exist.
        $envKey = strtoupper($section . '_' . implode('_', $keySegments));
        $envValue = Env::get($envKey);
        if ($envValue !== null) {
            return $envValue;
        }

        // Load if not loaded.
        if (!array_key_exists($section, $this->data)) {
            $this->data[$section] = $this->loader->load($section, Env::getAppEnvironment());
        }

        // Get data for digging.
        $data = [];
        if (array_key_exists($section, $this->data)) {
            $data = $this->data[$section];
        }

        // Dig down into data.
        foreach ($keySegments as $keySegment) {
            if (!is_array($data)) {
                $data = [];
            }

            if (!array_key_exists($keySegment, $data)) {
                return $default;
            }

            $data = $data[$keySegment];
        }

        return $data;
    }
}
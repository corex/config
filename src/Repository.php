<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Config\Exceptions\ConfigException;
use CoRex\Helpers\Arr;
use Symfony\Component\Finder\Finder;

class Repository
{
    /** @var string */
    private $path;

    /** @var string[] */
    private $environments;

    /** @var string */
    private $environment;

    /** @var mixed[] */
    private $items;

    /**
     * Repository.
     *
     * @param string $path
     * @throws ConfigException
     */
    public function __construct(string $path)
    {
        $this->clear();
        $this->path = $path;
        $this->environments = Environment::environments();

        // Get and validate environment.
        $this->environment = Config::appEnvironment();
        if (!Environment::isSupported($this->environment)) {
            throw new ConfigException('Environment ' . $this->environment . ' not supported.');
        }

        $this->reload();
    }

    /**
     * Clear.
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed $defaultValue Default null.
     * @return mixed
     */
    public function get(string $key, $defaultValue = null)
    {
        return Arr::get($this->items, $key, $defaultValue);
    }

    /**
     * Get integer.
     *
     * @param string $key
     * @param int $defaultValue Default 0.
     * @return int
     */
    public function getInt(string $key, int $defaultValue = 0): int
    {
        return intval($this->get($key, $defaultValue));
    }

    /**
     * Get boolean.
     *
     * @param string $key
     * @param bool $defaultValue Default false.
     * @return bool
     */
    public function getBool(string $key, bool $defaultValue = false): bool
    {
        $value = $this->get($key, $defaultValue);
        if (is_string($value)) {
            $value = strtolower($value);
        }
        return in_array($value, [1, true, '1', 'true', 'yes']);
    }

    /**
     * Set.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        Arr::set($this->items, $key, $value, true);
    }

    /**
     * Remove key.
     *
     * @param string $key
     */
    public function remove(string $key): void
    {
        $this->items = Arr::remove($this->items, $key);
    }

    /**
     * All.
     *
     * @return mixed[]
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Reload.
     */
    public function reload(): void
    {
        $this->clear();
        $this->items = $this->loadFiles();
    }

    /**
     * Load files.
     *
     * @return string[]
     */
    private function loadFiles(): array
    {
        $items = [];
        if (!is_dir($this->path)) {
            return $items;
        }

        $entries = Finder::create()->files()->name('*.php')->in($this->path);

        // Load files.
        foreach ($entries as $entry) {
            $basename = call_user_func([$entry, 'getBasename']);

            if ($this->isEnvironmentFilename($basename)) {
                // Do not process this file. It will be handled later.
                continue;
            }

            $pathRelative = call_user_func([$entry, 'getRelativePath']);
            $configKey = rtrim($basename, '.php');

            // Load main config-file.
            $this->loadFile($items, $this->path, $pathRelative, $configKey);

            // Load environment config-file.
            $this->loadFile($items, $this->path, $pathRelative, $configKey, $this->environment);
        }

        return $items;
    }

    /**
     * Load file.
     *
     * @param mixed[] $items
     * @param string $path
     * @param string $pathRelative
     * @param string $configKey
     * @param mixed $environment Default null.
     */
    private function loadFile(
        array &$items,
        string $path,
        string $pathRelative,
        string $configKey,
        $environment = null
    ): void {
        // Build filename.
        $filename = $path;
        if ($pathRelative !== null && $pathRelative !== '') {
            $filename .= '/' . $pathRelative;
        }
        $filename .= '/' . $configKey;
        if ($environment !== null) {
            $filename .= '.' . (string)$environment;
        }
        $filename .= '.php';

        // Load configuration.
        if (!file_exists($filename)) {
            return;
        }
        $config = require($filename);

        // Parse config.
        $itemsData = &$items;
        if ($pathRelative !== '') {
            $pathSegments = explode('.', $pathRelative);
            foreach ($pathSegments as $pathSegment) {
                $itemsData = &$itemsData[$pathSegment];
            }
        }
        if (!isset($itemsData[$configKey])) {
            $itemsData[$configKey] = null;
        }
        if (is_array($itemsData[$configKey]) && is_array($config)) {
            $itemsData[$configKey] = array_merge($itemsData[$configKey], $config);
        } else {
            $itemsData[$configKey] = $config;
        }
    }

    /**
     * Is environment filename.
     *
     * @param string $filename
     * @return bool
     */
    private function isEnvironmentFilename(string $filename): bool
    {
        $filenameEnding = substr($filename, strpos($filename, '.'));
        foreach ($this->environments as $environment) {
            if ($filenameEnding === '.' . $environment . '.php') {
                return true;
            }
        }
        return false;
    }
}
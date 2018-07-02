<?php

namespace CoRex\Config;

use CoRex\Support\Arr;
use Symfony\Component\Finder\Finder;

class Repository
{
    private $path;
    private $environments;
    private $environment;
    private $items;

    /**
     * Repository constructor.
     *
     * @param string $path
     * @throws ConfigException
     */
    public function __construct($path)
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
    public function clear()
    {
        $this->items = [];
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
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
    public function get($key, $defaultValue = null)
    {
        return Arr::get($this->items, $key, $defaultValue);
    }

    /**
     * Get integer.
     *
     * @param string $key
     * @param integer $defaultValue Default 0.
     * @return integer
     */
    public function getInt($key, $defaultValue = 0)
    {
        return intval($this->get($key, $defaultValue));
    }

    /**
     * Get boolean.
     *
     * @param string $key
     * @param boolean $defaultValue Default false.
     * @return boolean
     */
    public function getBool($key, $defaultValue = false)
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
    public function set($key, $value)
    {
        Arr::set($this->items, $key, $value, true);
    }

    /**
     * Remove key.
     *
     * @param string $key
     */
    public function remove($key)
    {
        $this->items = Arr::remove($this->items, $key);
    }

    /**
     * All.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Reload.
     */
    public function reload()
    {
        $this->clear();
        $this->items = $this->loadFiles();
    }

    /**
     * Load files.
     *
     * @return array
     */
    private function loadFiles()
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
     * @param array $items
     * @param string $path
     * @param string $pathRelative
     * @param string $configKey
     * @param mixed $environment Default null.
     */
    private function loadFile(array &$items, $path, $pathRelative, $configKey, $environment = null)
    {
        // Build filename.
        $filename = $path;
        if ($pathRelative !== null && $pathRelative != '') {
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
        if ($pathRelative != '') {
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
     * @return boolean
     */
    private function isEnvironmentFilename($filename)
    {
        $filenameEnding = substr($filename, strpos($filename, '.'));
        foreach ($this->environments as $environment) {
            if ($filenameEnding == '.' . $environment . '.php') {
                return true;
            }
        }
        return false;
    }
}
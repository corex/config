<?php

namespace CoRex\Config;

use Illuminate\Config\Repository as IlluminateRepository;
use Symfony\Component\Finder\Finder;

class Repository extends IlluminateRepository
{
    private $path;

    /**
     * Repository constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->clear();
        $this->path = $path;
        $items = $this->loadFiles();
        parent::__construct($items);
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
            $pathRelative = call_user_func([$entry, 'getRelativePath']);
            $configKey = rtrim($basename, '.php');
            $filename = $this->path . '/';
            if ($pathRelative != '') {
                $filename .= $pathRelative . '/';
            }
            $filename .= $basename;
            if (!file_exists($filename)) {
                continue;
            }
            $config = self::loadFile($filename);
            $itemsData = &$items;
            if ($pathRelative != '') {
                $pathSegments = explode('.', $pathRelative);
                foreach ($pathSegments as $pathSegment) {
                    $itemsData = &$itemsData[$pathSegment];
                }
            }
            $itemsData[$configKey] = $config;
        }
        return $items;
    }

    /**
     * Load file.
     *
     * @param string $filename
     * @param mixed $defaultValue Default null.
     * @return mixed
     */
    private function loadFile($filename, $defaultValue = null)
    {
        if (!file_exists($filename)) {
            return $defaultValue;
        }
        return require($filename);
    }
}
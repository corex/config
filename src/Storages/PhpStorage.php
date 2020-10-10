<?php

declare(strict_types=1);

namespace CoRex\Config\Storages;

use CoRex\Config\Exceptions\StorageException;
use CoRex\Config\Interfaces\StorageInterface;

class PhpStorage implements StorageInterface
{
    /** @var string */
    private $path;

    /**
     * FileStorage.
     *
     * @param string $path
     * @throws StorageException
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');

        // Validate storage path.
        if (!is_dir($this->path)) {
            throw new StorageException('Storage path is not valid.');
        }
    }

    /**
     * Load configuration.
     *
     * @param string $section
     * @param string $environment
     * @return mixed[]
     */
    public function load(string $section, string $environment): array
    {
        $data = [];

        // Require main file.
        $filename = $this->path . '/' . $section . '.php';
        if (file_exists($filename)) {
            $data = require $filename;
        }

        // Require environment.
        $filename = $this->path . '/' . $environment . '/' . $section . '.php';
        if (file_exists($filename)) {
            $environmentData = require $filename;
            $data = array_replace_recursive($data, $environmentData);
        }

        return $data;
    }
}
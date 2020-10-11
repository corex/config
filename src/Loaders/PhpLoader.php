<?php

declare(strict_types=1);

namespace CoRex\Config\Loaders;

use CoRex\Config\Exceptions\LoaderException;
use CoRex\Config\Interfaces\LoaderInterface;

class PhpLoader implements LoaderInterface
{
    /** @var string */
    private $path;

    /**
     * PhpLoader constructor.
     *
     * @param string $path
     * @throws LoaderException
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');

        // Validate path.
        if (!is_dir($this->path)) {
            throw new LoaderException('Loader path is not valid.');
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
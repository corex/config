<?php

declare(strict_types=1);

namespace CoRex\Config\Filesystem;

use Composer\Autoload\ClassLoader;
use CoRex\Config\Exceptions\ConfigException;
use ReflectionClass;

class Filesystem implements FilesystemInterface
{
    /**
     * @inheritDoc
     */
    public function getRootPath(?string $additional = null): string
    {
        // Determine path to {root} of project by composer class loader.
        $reflectionClass = new ReflectionClass(ClassLoader::class);
        $composerClassLoaderFilename = $reflectionClass->getFileName();

        $path = rtrim(dirname((string)$composerClassLoaderFilename, 3), '/');
        if ($additional !== null) {
            $path .= '/' . $additional;
        }

        return $path;
    }

    /**
     * @inheritDoc
     */
    public function directoryExists(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * @inheritDoc
     */
    public function fileExists(string $filename): bool
    {
        return file_exists($filename) && is_file($filename);
    }

    /**
     * @inheritDoc
     */
    public function requireFileArray(string $filename): array
    {
        // Clear output buffer in the situation a fault occurs.
        ob_start();
        $data = require $filename;
        ob_end_clean();

        if (!is_array($data)) {
            throw new ConfigException(
                sprintf(
                    'Data from file "%s" is not an array.',
                    $filename
                )
            );
        }

        return $data;
    }
}
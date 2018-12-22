<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Filesystem\Path as FilesystemPath;

class Path extends FilesystemPath
{
    /**
     * Get package path.
     *
     * @return string
     */
    protected static function packagePath(): string
    {
        return dirname(__DIR__);
    }
}
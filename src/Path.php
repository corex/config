<?php

namespace CoRex\Config;

use CoRex\Support\System\Path as SupportPath;

class Path extends SupportPath
{
    /**
     * Get package path.
     *
     * @return string
     */
    protected static function packagePath()
    {
        return dirname(dirname(__DIR__));
    }
}
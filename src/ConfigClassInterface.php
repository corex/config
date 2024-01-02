<?php

declare(strict_types=1);

namespace CoRex\Config;

interface ConfigClassInterface
{
    /**
     * Get section (first part of config key).
     *
     * @return string
     */
    public static function getSection(): string;
}
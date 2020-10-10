<?php

declare(strict_types=1);

namespace CoRex\Config\Interfaces;

interface StorageInterface
{
    /**
     * Load configuration.
     *
     * @param string $section
     * @param string $environment
     * @return mixed[]
     */
    public function load(string $section, string $environment): array;
}
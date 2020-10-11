<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\HelperClasses;

use CoRex\Config\Interfaces\LoaderInterface;

class ArrayLoader implements LoaderInterface
{
    /** @var mixed[] */
    private $data;

    /**
     * ArrayLoader.
     *
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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
        if (!array_key_exists($section, $this->data)) {
            return [];
        }

        return $this->data[$section];
    }
}
<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Config\Data\ValueGetInterface;
use CoRex\Config\Section\SectionInterface;

interface ConfigInterface extends ValueGetInterface
{
    /**
     * Get section builder.
     *
     * @param string $section
     * @return SectionInterface
     */
    public function section(string $section): SectionInterface;

    /**
     * Get array and pass on config class constructor.
     *
     * Note: a config class must implement ConfigClassInterface.
     *
     * @param string $configClass
     * @return ConfigClassInterface
     */
    public function getConfigClassObject(string $configClass): ConfigClassInterface;
}
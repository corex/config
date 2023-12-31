<?php

declare(strict_types=1);

namespace CoRex\Config\Section;

use CoRex\Config\ConfigInterface;

interface SectionInterface extends ConfigInterface
{
    /**
     * Get section.
     *
     * @return string
     */
    public function getSection(): string;
}
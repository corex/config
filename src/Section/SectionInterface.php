<?php

declare(strict_types=1);

namespace CoRex\Config\Section;

use CoRex\Config\Data\ValueGetInterface;

interface SectionInterface extends ValueGetInterface
{
    /**
     * Get section.
     *
     * @return string
     */
    public function getSection(): string;
}
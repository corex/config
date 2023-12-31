<?php

declare(strict_types=1);

namespace CoRex\Config\Section;

interface SectionBuilderInterface
{
    /**
     * Get section builder.
     *
     * @param string $section
     * @return SectionInterface
     */
    public function section(string $section): SectionInterface;
}
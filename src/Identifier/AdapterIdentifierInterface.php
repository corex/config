<?php

declare(strict_types=1);

namespace CoRex\Config\Identifier;

interface AdapterIdentifierInterface
{
    /**
     * Get name of class.
     *
     * @return class-string
     */
    public function getClassName(): string;

    /**
     * Get hash for class-name and arguments.
     *
     * @return string
     */
    public function getHash(): string;
}
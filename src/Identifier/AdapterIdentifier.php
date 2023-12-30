<?php

declare(strict_types=1);

namespace CoRex\Config\Identifier;

use CoRex\Config\Exceptions\IdentifierException;

class AdapterIdentifier implements AdapterIdentifierInterface
{
    /** @var class-string */
    private string $className;
    private string $hash;

    /**
     * @param class-string $className
     * @param array<mixed, mixed> $arguments
     */
    public function __construct(string $className, array $arguments)
    {
        if (!class_exists($className)) {
            throw new IdentifierException(
                sprintf(
                    'Class "%s" not found.',
                    $className
                )
            );
        }

        $this->className = $className;
        $this->hash = hash('sha256', $className . ':' . serialize($arguments));
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @inheritDoc
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}
<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Identifier\AdapterIdentifier;
use CoRex\Config\Identifier\AdapterIdentifierInterface;
use ReflectionClass;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @inheritDoc
     */
    public function getIdentifier(): AdapterIdentifierInterface
    {
        $className = get_class($this);

        $reflectionClass = new ReflectionClass($this);
        $constructor = $reflectionClass->getConstructor();

        $resolvedParameterNames = [];

        if ($constructor !== null) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $resolvedParameterNames[] = $parameter->getName();
            }
        }

        return new AdapterIdentifier($className, $resolvedParameterNames);
    }
}
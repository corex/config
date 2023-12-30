<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Adapter;

use CoRex\Config\Adapter\AbstractAdapter;
use CoRex\Config\Data\Value;
use CoRex\Config\Identifier\AdapterIdentifier;
use CoRex\Config\Key\KeyInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers \CoRex\Config\Adapter\AbstractAdapter
 */
class AbstractAdapterTest extends TestCase
{
    public function testGetIdentifier(): void
    {
        $adapter = new class ('James', 'Bond') extends AbstractAdapter {
            private string $firstname; // @phpstan-ignore-line
            private string $lastname; // @phpstan-ignore-line

            public function __construct(string $firstname, string $lastname)
            {
                $this->firstname = $firstname;
                $this->lastname = $lastname;
            }

            /**
             * @inheritDoc
             */
            public function getValue(KeyInterface $key): Value
            {
                return new Value(null, $key, null);
            }
        };

        $expectedClassName = get_class($adapter);

        $reflectionClass = new ReflectionClass($adapter);
        $constructor = $reflectionClass->getConstructor();

        $resolvedParameterNames = [];

        if ($constructor !== null) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $resolvedParameterNames[] = $parameter->getName();
            }
        }

        $expectedIdentifier = new AdapterIdentifier($expectedClassName, $resolvedParameterNames);

        $this->assertEquals($expectedIdentifier, $adapter->getIdentifier());
        $this->assertSame(
            $expectedIdentifier->getClassName(),
            $adapter->getIdentifier()->getClassName()
        );
        $this->assertSame(
            $expectedIdentifier->getHash(),
            $adapter->getIdentifier()->getHash()
        );
    }
}
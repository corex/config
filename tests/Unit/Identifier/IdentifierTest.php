<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Identifier;

use CoRex\Config\Data\Value;
use CoRex\Config\Exceptions\IdentifierException;
use CoRex\Config\Identifier\AdapterIdentifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Identifier\AdapterIdentifier
 */
class IdentifierTest extends TestCase
{
    public function testConstructorWorks(): void
    {
        $arguments = ['argument1', 'argument2'];
        $identifier = new AdapterIdentifier(Value::class, $arguments);

        $this->assertSame(Value::class, $identifier->getClassName());
        $this->assertSame(
            hash('sha256', Value::class . ':' . serialize($arguments)),
            $identifier->getHash()
        );
    }

    public function testConstructorWhenClassNotFound(): void
    {
        /** @var class-string $className */
        $className = 'not-a-class';

        $this->expectException(IdentifierException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Class "%s" not found.',
                $className
            )
        );

        new AdapterIdentifier($className, []);
    }
}
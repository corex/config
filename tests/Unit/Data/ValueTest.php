<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Data;

use CoRex\Config\Adapter\AdapterInterface;
use CoRex\Config\Data\Value;
use CoRex\Config\Key\KeyInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Data\Value
 */
class ValueTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWorks(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $key = $this->createMock(KeyInterface::class);
        $value = 'a.value';

        $valueObject = new Value($adapter, $key, $value);

        $this->assertSame($adapter, $valueObject->getAdapter());
        $this->assertTrue($valueObject->hasKey());
        $this->assertSame($key, $valueObject->getKey());
        $this->assertSame($value, $valueObject->getValue());
    }

    /**
     * @throws Exception
     */
    public function testValueNotFound(): void
    {
        $key = $this->createMock(KeyInterface::class);

        $valueObject = new Value(null, $key, null);

        $this->assertNull($valueObject->getAdapter());
        $this->assertFalse($valueObject->hasKey());
        $this->assertSame($key, $valueObject->getKey());
        $this->assertNull($valueObject->getValue());
    }
}
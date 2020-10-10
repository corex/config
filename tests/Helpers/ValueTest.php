<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Helpers;

use CoRex\Config\Helpers\Value;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    /**
     * Test getBool() resulting in true.
     */
    public function testIsTrue(): void
    {
        // Assert true.
        $this->assertTrue(Value::isTrue(1));
        $this->assertTrue(Value::isTrue(true));
        $this->assertTrue(Value::isTrue('1'));
        $this->assertTrue(Value::isTrue('true'));
        $this->assertTrue(Value::isTrue('yes'));
        $this->assertTrue(Value::isTrue('on'));

        // Assert false.
        $this->assertFalse(Value::isTrue(0));
        $this->assertFalse(Value::isTrue(false));
        $this->assertFalse(Value::isTrue('0'));
        $this->assertFalse(Value::isTrue('false'));
        $this->assertFalse(Value::isTrue('no'));
        $this->assertFalse(Value::isTrue('off'));
        $this->assertFalse(Value::isTrue('unknown'));
    }
}
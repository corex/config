<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Adapter;

use CoRex\Config\Adapter\EnvAdapter;
use CoRex\Config\Key\Key;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Adapter\EnvAdapter
 */
class EnvAdapterTest extends TestCase
{
    public function testGetValue(): void
    {
        $fullname = 'James Bond';
        $_ENV['ACTOR_NAME'] = $fullname; // phpcs:ignore

        $key = new Key('actor.name');

        $adapter = new EnvAdapter();
        $value = $adapter->getValue($key);

        $this->assertTrue($value->hasKey());

        $this->assertSame($fullname, $value->getValue());

        unset($_ENV['ACTOR_NAME']); // phpcs:disable
    }
}
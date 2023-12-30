<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Adapter;

use CoRex\Config\Adapter\ServerAdapter;
use CoRex\Config\Key\Key;
use CoRex\Config\Key\KeyType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Adapter\ServerAdapter
 */
class ServerAdapterTest extends TestCase
{
    public function testGetValue(): void
    {
        $fullname = 'James Bond';
        $_SERVER['ACTOR_NAME'] = $fullname; // phpcs:ignore

        $key = new Key(KeyType::MIXED, 'actor.name');

        $adapter = new ServerAdapter();
        $value = $adapter->getValue($key);

        $this->assertTrue($value->hasKey());

        $this->assertSame($fullname, $value->getValue());

        unset($_SERVER['ACTOR_NAME']);// phpcs:ignore
    }
}
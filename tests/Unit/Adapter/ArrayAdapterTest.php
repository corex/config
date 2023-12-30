<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Adapter;

use CoRex\Config\Adapter\ArrayAdapter;
use CoRex\Config\Key\Key;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Adapter\ArrayAdapter
 */
class ArrayAdapterTest extends TestCase
{
    public function testGetValue(): void
    {
        $data = [
            'actor' => [
                'name' => 'James Bond',
            ],
        ];

        $key = new Key('actor.name');

        $adapter = new ArrayAdapter($data);
        $value = $adapter->getValue($key);

        $this->assertTrue($value->hasKey());
        $this->assertSame(
            $data['actor']['name'],
            $value->getValue()
        );
    }
}
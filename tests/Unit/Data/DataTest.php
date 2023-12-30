<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Data;

use CoRex\Config\Data\Data;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Data\Data
 */
class DataTest extends TestCase
{
    public function testWhenNoParts(): void
    {
        $data = [
            'actor' => [
                'fullname' => 'James Bond',
            ]
        ];

        $this->assertSame(
            $data,
            Data::getFromArray($data, [])
        );
    }

    public function testWhenKeyPartsSpecified(): void
    {
        $data = [
            'actor' => [
                'fullname' => 'James Bond',
            ]
        ];

        $this->assertSame(
            $data['actor']['fullname'],
            Data::getFromArray($data, ['actor', 'fullname'])
        );
    }

    public function testWhenKeyNotFound(): void
    {
        $this->assertSame(
            Data::NO_VALUE_FOUND_CODE,
            Data::getFromArray([], ['unknown', 'key'])
        );
    }
}
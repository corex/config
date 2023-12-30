<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Adapter;

use CoRex\Config\Adapter\ArrayFileAdapter;
use CoRex\Config\Filesystem\FilesystemInterface;
use CoRex\Config\Key\Key;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Adapter\ArrayFileAdapter
 */
class ArrayFileAdapterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetValue(): void
    {
        $data = [
            'actor' => [
                'name' => 'James Bond'
            ]
        ];

        $filesystem = $this->createMock(FilesystemInterface::class);
        $filesystem->expects($this->once())
            ->method('fileExists')
            ->with('test/bond.php')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('requireFileArray')
            ->with('test/bond.php')
            ->willReturn($data);

        $key = new Key('bond.actor.name');

        $adapter = new ArrayFileAdapter($filesystem, 'test');
        $value = $adapter->getValue($key);

        $this->assertTrue($value->hasKey());
        $this->assertSame(
            $data['actor']['name'],
            $value->getValue()
        );
    }
}
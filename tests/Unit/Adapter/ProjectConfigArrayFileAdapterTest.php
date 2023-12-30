<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Adapter;

use CoRex\Config\Adapter\ProjectConfigArrayFileAdapter;
use CoRex\Config\Exceptions\AdapterException;
use CoRex\Config\Filesystem\FilesystemInterface;
use CoRex\Config\Key\Key;
use CoRex\Config\Key\KeyType;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Adapter\ProjectConfigArrayFileAdapter
 */
class ProjectConfigArrayFileAdapterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetValueWorks(): void
    {
        $data = [
            'actor' => [
                'name' => 'James Bond'
            ]
        ];

        $filesystem = $this->createMock(FilesystemInterface::class);
        $filesystem->expects($this->once())
            ->method('getRootPath')
            ->with('config')
            ->willReturn('/my/config/files/config');
        $filesystem->expects($this->once())
            ->method('directoryExists')
            ->with('/my/config/files/config')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('fileExists')
            ->with('/my/config/files/config/bond.php')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('requireFileArray')
            ->with('/my/config/files/config/bond.php')
            ->willReturn($data);

        $key = new Key(KeyType::MIXED, 'bond.actor.name');

        $adapter = new ProjectConfigArrayFileAdapter($filesystem);
        $value = $adapter->getValue($key);

        $this->assertTrue($value->hasKey());
        $this->assertSame(
            $data['actor']['name'],
            $value->getValue()
        );
    }

    /**
     * @throws Exception
     */
    public function testGetValueWhenDirectoryNotFound(): void
    {
        $path = '/my/config/files/config';

        $filesystem = $this->createMock(FilesystemInterface::class);
        $filesystem->expects($this->once())
            ->method('getRootPath')
            ->with('config')
            ->willReturn($path);
        $filesystem->expects($this->once())
            ->method('directoryExists')
            ->with($path)
            ->willReturn(false);

        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Path "%s" does not exist.',
                $path
            )
        );

        new ProjectConfigArrayFileAdapter($filesystem);
    }
}
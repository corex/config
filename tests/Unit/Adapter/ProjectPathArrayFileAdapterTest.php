<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Adapter;

use CoRex\Config\Adapter\ProjectPathArrayFileAdapter;
use CoRex\Config\Exceptions\AdapterException;
use CoRex\Config\Filesystem\FilesystemInterface;
use CoRex\Config\Key\Key;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Adapter\ProjectPathArrayFileAdapter
 */
class ProjectPathArrayFileAdapterTest extends TestCase
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
            ->with('test')
            ->willReturn('/my/config/files/test');
        $filesystem->expects($this->once())
            ->method('directoryExists')
            ->with('/my/config/files/test')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('fileExists')
            ->with('/my/config/files/test/bond.php')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('requireFileArray')
            ->with('/my/config/files/test/bond.php')
            ->willReturn($data);

        $key = new Key('bond.actor.name');

        $adapter = new ProjectPathArrayFileAdapter($filesystem, 'test');
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
        $path = '/my/config/files/test';

        $filesystem = $this->createMock(FilesystemInterface::class);
        $filesystem->expects($this->once())
            ->method('getRootPath')
            ->with('test')
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

        new ProjectPathArrayFileAdapter($filesystem, 'test');
    }
}
<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit;

use CoRex\Config\ConfigFactory;
use CoRex\Config\ConfigFactoryInterface;
use CoRex\Config\ConfigInterface;
use CoRex\Config\Filesystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;

class ConfigFactoryTest extends TestCase
{
    public function testCreateWithProjectConfigArrayFileAdapter(): void
    {
        $this->assertInstanceOf(
            ConfigInterface::class,
            $this->createConfigFactory(true)->createWithProjectConfigArrayFileAdapter()
        );
    }

    public function testCreateWithProjectPathArrayFileAdapter(): void
    {
        $this->assertInstanceOf(
            ConfigInterface::class,
            $this->createConfigFactory(true)->createWithProjectPathArrayFileAdapter('config')
        );
    }

    public function testCreateWithServerAndEnvAndProjectConfigArrayFileAdapter(): void
    {
        $this->assertInstanceOf(
            ConfigInterface::class,
            $this->createConfigFactory(true)->createWithServerAndEnvAndProjectConfigArrayFileAdapter()
        );
    }

    public function testCreateWithServerAndEnvAndProjectPathArrayFileAdapter(): void
    {
        $this->assertInstanceOf(
            ConfigInterface::class,
            $this->createConfigFactory(true)->createWithServerAndEnvAndProjectPathArrayFileAdapter('config')
        );
    }

    public function testCreateWithServerAndEnvAdapters(): void
    {
        $this->assertInstanceOf(
            ConfigInterface::class,
            $this->createConfigFactory(false)->createWithServerAndEnvAdapter()
        );
    }

    private function createConfigFactory(bool $mockFilesystem): ConfigFactoryInterface
    {
        $path = '/my/config/files/config';

        $filesystem = $this->createMock(FilesystemInterface::class);
        if ($mockFilesystem) {
            $filesystem->expects($this->once())
                ->method('getRootPath')
                ->with('config')
                ->willReturn($path);
            $filesystem->expects($this->once())
                ->method('directoryExists')
                ->with($path)
                ->willReturn(true);
        }

        return new ConfigFactory($filesystem);
    }
}
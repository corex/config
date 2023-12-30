<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Filesystem;

use Composer\Autoload\ClassLoader;
use CoRex\Config\Exceptions\ConfigException;
use CoRex\Config\Filesystem\Filesystem;
use CoRex\Config\Filesystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers \CoRex\Config\Filesystem\Filesystem
 */
class FilesystemTest extends TestCase
{
    private string $rootPath;
    private FilesystemInterface $filesystem;

    public function testGetRoot(): void
    {
        $this->assertSame($this->rootPath, $this->filesystem->getRootPath());
        $this->assertSame($this->rootPath . '/test', $this->filesystem->getRootPath('test'));
    }

    public function testDirectoryExists(): void
    {
        $this->assertTrue($this->filesystem->directoryExists($this->rootPath . '/src'));
        $this->assertFalse($this->filesystem->directoryExists($this->rootPath . '/unknown'));
    }

    public function testFileExists(): void
    {
        $this->assertTrue($this->filesystem->fileExists($this->rootPath . '/composer.json'));
        $this->assertFalse($this->filesystem->fileExists($this->rootPath . '/unknown'));
    }

    public function testRequireFileArrayWorks(): void
    {
        $data = require $this->rootPath . '/tests/Resource/config/bond.php';
        $this->assertSame(
            $data,
            $this->filesystem->requireFileArray('tests/Resource/config/bond.php')
        );
    }

    public function testRequireFileArrayWhenNotAnArrayFile(): void
    {
        $filename = 'tests/Resource/config/not-an-array-file.php';

        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Data from file "%s" is not an array.',
                $filename
            )
        );

        $this->filesystem->requireFileArray($filename);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Determine path to {root} of project by composer class loader.
        $reflectionClass = new ReflectionClass(ClassLoader::class);
        $this->rootPath = dirname((string)$reflectionClass->getFileName(), 3);

        $this->filesystem = new Filesystem();
    }
}
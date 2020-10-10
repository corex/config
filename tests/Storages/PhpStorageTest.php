<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Storages;

use CoRex\Config\Env;
use CoRex\Config\Exceptions\StorageException;
use CoRex\Config\Interfaces\StorageInterface;
use CoRex\Config\Storages\PhpStorage;
use CoRex\Helpers\Obj;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class PhpStorageTest extends TestCase
{
    /** @var string */
    private $path;

    /**
     * Test implementing storage interface.
     */
    public function testImplementingStorageInterface(): void
    {
        $this->assertTrue(Obj::hasInterface(PhpStorage::class, StorageInterface::class));
    }

    /**
     * Test constructor.
     *
     * @throws StorageException
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $storage = new PhpStorage($this->path);
        $this->assertSame($this->path, Obj::getProperty('path', $storage));
    }

    /**
     * Test constructor with invalid path.
     */
    public function testConstructorWithInvalidPath(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('Storage path is not valid.');
        new PhpStorage('/a/path/which/does/not/exist');
    }

    /**
     * Test load.
     *
     * @throws StorageException
     */
    public function testLoad(): void
    {
        $storage = new PhpStorage($this->path);
        $check = require $this->path . '/bond.php';
        $data = $storage->load('bond', Env::PRODUCTION);
        $this->assertSame($check, $data);
    }

    /**
     * Test load with environment testing.
     *
     * @throws StorageException
     */
    public function testLoadTesting(): void
    {
        $storage = new PhpStorage($this->path);

        $check = array_replace_recursive(
            require $this->path . '/bond.php',
            require $this->path . '/testing/bond.php'
        );

        $data = $storage->load('bond', Env::TESTING);

        $this->assertSame($check, $data);
    }

    /**
     * Test load() when not found.
     *
     * @throws StorageException
     */
    public function testLoadNotFound(): void
    {
        $storage = new PhpStorage($this->path);
        $this->assertSame([], $storage->load('unknown', Env::PRODUCTION));
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->path = dirname(__DIR__) . '/files/default';
    }
}
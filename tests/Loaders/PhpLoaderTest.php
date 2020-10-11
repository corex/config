<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Loaders;

use CoRex\Config\Env;
use CoRex\Config\Exceptions\LoaderException;
use CoRex\Config\Interfaces\LoaderInterface;
use CoRex\Config\Loaders\PhpLoader;
use CoRex\Helpers\Obj;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class PhpLoaderTest extends TestCase
{
    /** @var string */
    private $path;

    /**
     * Test implementing loader interface.
     */
    public function testImplementingLoaderInterface(): void
    {
        $this->assertTrue(Obj::hasInterface(PhpLoader::class, LoaderInterface::class));
    }

    /**
     * Test constructor.
     *
     * @throws LoaderException
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $loader = new PhpLoader($this->path);
        $this->assertSame($this->path, Obj::getProperty('path', $loader));
    }

    /**
     * Test constructor with invalid path.
     */
    public function testConstructorWithInvalidPath(): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage('Loader path is not valid.');
        new PhpLoader('/a/path/which/does/not/exist');
    }

    /**
     * Test load.
     *
     * @throws LoaderException
     */
    public function testLoad(): void
    {
        $loader = new PhpLoader($this->path);
        $check = require $this->path . '/bond.php';
        $data = $loader->load('bond', Env::PRODUCTION);
        $this->assertSame($check, $data);
    }

    /**
     * Test load with environment testing.
     *
     * @throws LoaderException
     */
    public function testLoadTesting(): void
    {
        $loader = new PhpLoader($this->path);

        $check = array_replace_recursive(
            require $this->path . '/bond.php',
            require $this->path . '/testing/bond.php'
        );

        $data = $loader->load('bond', Env::TESTING);

        $this->assertSame($check, $data);
    }

    /**
     * Test load() when not found.
     *
     * @throws LoaderException
     */
    public function testLoadNotFound(): void
    {
        $loader = new PhpLoader($this->path);
        $this->assertSame([], $loader->load('unknown', Env::PRODUCTION));
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
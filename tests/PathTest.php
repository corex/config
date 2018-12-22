<?php

declare(strict_types=1);

namespace Tests\CoRex\Config;

use CoRex\Config\Path;
use CoRex\Helpers\Obj;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /**
     * Test package path.
     *
     * @throws \ReflectionException
     */
    public function testPackagePath(): void
    {
        $path = Obj::callMethod('packagePath', null, [], Path::class);
        $this->assertEquals(dirname(__DIR__), $path);
    }
}
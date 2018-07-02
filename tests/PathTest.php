<?php

namespace Tests\CoRex\Config;

use CoRex\Config\Path;
use CoRex\Support\Obj;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /**
     * Test package path.
     */
    public function testPackagePath()
    {
        $path = Obj::callMethod('packagePath', null, [], Path::class);
        $this->assertEquals(dirname(__DIR__), $path);
    }
}
<?php

declare(strict_types=1);

namespace Tests\CoRex\Config;

use CoRex\Config\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    /**
     * Test getEnvironments.
     *
     * @throws \ReflectionException
     */
    public function testGetEnvironments(): void
    {
        // Fetch environment constants from class directly.
        $reflectionClass = new \ReflectionClass(Environment::class);
        $environmentConstants = array_values($reflectionClass->getConstants());
        sort($environmentConstants);

        // Call method to get environments.
        $environments = Environment::environments();
        sort($environments);

        $this->assertEquals($environmentConstants, $environments);
    }

    /**
     * Test isSupported.
     */
    public function testIsSupported(): void
    {
        $this->assertTrue(Environment::isSupported(Environment::LOCAL));
        $this->assertTrue(Environment::isSupported(Environment::TESTING));
        $this->assertTrue(Environment::isSupported(Environment::PRODUCTION));
        $this->assertFalse(Environment::isSupported('unknown'));
    }
}

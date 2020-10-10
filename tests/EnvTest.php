<?php

declare(strict_types=1);

namespace Tests\CoRex\Config;

use CoRex\Config\Env;
use CoRex\Config\Exceptions\EnvironmentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class EnvTest extends TestCase
{
    /** @var int */
    private $value;

    /** @var string */
    private $value1;

    /** @var string */
    private $value2;

    /** @var string */
    private $value3;

    /**
     * Test getEnvironments.
     */
    public function testGetEnvironments(): void
    {
        // Fetch environment constants from class directly.
        $reflectionClass = new ReflectionClass(Env::class);
        $environmentConstants = array_values($reflectionClass->getConstants());
        sort($environmentConstants);

        // Call method to get environments.
        $environments = Env::getEnvironments();
        sort($environments);

        $this->assertSame($environmentConstants, $environments);
    }

    /**
     * Test isSupported.
     */
    public function testIsSupported(): void
    {
        $this->assertTrue(Env::isSupported(Env::LOCAL));
        $this->assertTrue(Env::isSupported(Env::TESTING));
        $this->assertTrue(Env::isSupported(Env::PRODUCTION));
        $this->assertFalse(Env::isSupported('unknown'));
    }

    /**
     * Test get() unknown.
     */
    public function testGetUnknown(): void
    {
        $this->assertNull(Env::get('unknown'));
    }

    /**
     * Test get() via getenv.
     */
    public function testGetGetEnv(): void
    {
        putenv('MY_KEY=' . $this->value1);
        $_ENV['MY_KEY'] = $this->value2;
        $_SERVER['MY_KEY'] = $this->value3;
        $this->assertSame($this->value1, Env::get('MY_KEY'));
    }

    /**
     * Test get() via $_ENV.
     */
    public function testGetEnvArray(): void
    {
        putenv('MY_KEY');
        $_ENV['MY_KEY'] = $this->value2;
        $_SERVER['MY_KEY'] = $this->value3;
        $this->assertSame($this->value2, Env::get('MY_KEY'));
    }

    /**
     * Test get() via $_SERVER.
     */
    public function testGetServerArray(): void
    {
        putenv('MY_KEY');
        if (array_key_exists('MY_KEY', $_ENV)) {
            unset($_ENV['MY_KEY']);
        }
        $_SERVER['MY_KEY'] = $this->value3;
        $this->assertSame($this->value3, Env::get('MY_KEY'));
    }

    /**
     * Test getString().
     */
    public function testGetString(): void
    {
        $_SERVER['MY_KEY'] = $this->value;
        $this->assertSame((string)$this->value, Env::getString('MY_KEY'));
    }

    /**
     * Test getInt().
     */
    public function testGetInt(): void
    {
        $_SERVER['MY_KEY'] = (string)$this->value;
        $this->assertSame($this->value, Env::getInt('MY_KEY'));
    }

    /**
     * Test getBool() resulting in true.
     */
    public function testGetBoolTrue(): void
    {
        // 1
        $_SERVER['MY_KEY'] = 1;
        $this->assertTrue(Env::getBool('MY_KEY'));

        // true
        $_SERVER['MY_KEY'] = true;
        $this->assertTrue(Env::getBool('MY_KEY'));

        // '1'
        $_SERVER['MY_KEY'] = '1';
        $this->assertTrue(Env::getBool('MY_KEY'));

        // 'true'
        $_SERVER['MY_KEY'] = 'true';
        $this->assertTrue(Env::getBool('MY_KEY'));

        // 'yes'
        $_SERVER['MY_KEY'] = 'yes';
        $this->assertTrue(Env::getBool('MY_KEY'));

        // 'on'
        $_SERVER['MY_KEY'] = 'on';
        $this->assertTrue(Env::getBool('MY_KEY'));
    }

    /**
     * Test getBool() resulting in false.
     */
    public function testGetBoolFalse(): void
    {
        // 0
        $_SERVER['MY_KEY'] = 0;
        $this->assertFalse(Env::getBool('MY_KEY'));

        // false
        $_SERVER['MY_KEY'] = false;
        $this->assertFalse(Env::getBool('MY_KEY'));

        // '0'
        $_SERVER['MY_KEY'] = '0';
        $this->assertFalse(Env::getBool('MY_KEY'));

        // 'false'
        $_SERVER['MY_KEY'] = 'false';
        $this->assertFalse(Env::getBool('MY_KEY'));

        // 'no'
        $_SERVER['MY_KEY'] = 'no';
        $this->assertFalse(Env::getBool('MY_KEY'));

        // 'off'
        $_SERVER['MY_KEY'] = 'off';
        $this->assertFalse(Env::getBool('MY_KEY'));

        // 'unknown'
        $_SERVER['MY_KEY'] = 'unknown';
        $this->assertFalse(Env::getBool('MY_KEY'));
    }

    /**
     * Test getAppName().
     */
    public function testGetAppName(): void
    {
        $this->var('APP_NAME', null);
        $this->assertNull(Env::getAppName());

        $this->var('APP_NAME', $this->value1);
        $this->assertSame($this->value1, Env::getAppName());
    }

    /**
     * Test getAppEnvironment() as local.
     *
     * @throws EnvironmentException
     */
    public function testGetAppEnvironmentAsLocal(): void
    {
        $this->var('APP_ENV', Env::LOCAL);
        $this->assertSame(Env::LOCAL, Env::getAppEnvironment());
    }

    /**
     * Test getAppEnvironment() as testing.
     *
     * @throws EnvironmentException
     */
    public function testGetAppEnvironmentAsTesting(): void
    {
        $this->var('APP_ENV', Env::TESTING);
        $this->assertSame(Env::TESTING, Env::getAppEnvironment());
    }

    /**
     * Test getAppEnvironment() as production.
     *
     * @throws EnvironmentException
     */
    public function testGetAppEnvironmentAsProduction(): void
    {
        $this->var('APP_ENV', Env::PRODUCTION);
        $this->assertSame(Env::PRODUCTION, Env::getAppEnvironment());
    }

    /**
     * Test getAppEnvironment() as unknown/exception.
     *
     * @throws EnvironmentException
     */
    public function testGetAppEnvironmentUnknown(): void
    {
        $this->var('APP_ENV', 'unknown');
        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage('Environment unknown not supported.');
        Env::getAppEnvironment();
    }

    /**
     * Test getAppEnvironment() not set..
     *
     * @throws EnvironmentException
     */
    public function testGetAppEnvironmentNotSet(): void
    {
        $this->var('APP_ENV', null);
        $this->assertSame(Env::PRODUCTION, Env::getAppEnvironment());
    }

    /**
     * Test getAppDebug().
     */
    public function testGetAppDebug(): void
    {
        $this->var('APP_DEBUG', null);
        $this->assertFalse(Env::getAppDebug());

        $this->var('APP_DEBUG', 'yes');
        $this->assertTrue(Env::getAppDebug());
    }

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->value = random_int(1, 100000);

        $randomValue = md5((string)$this->value);

        $this->value1 = $randomValue . '_1';
        $this->value2 = $randomValue . '_2';
        $this->value3 = $randomValue . '_3';
    }

    /**
     * Var.
     *
     * @param string $key
     * @param mixed|null $value
     * @return $this
     */
    private function var(string $key, $value): self
    {
        // Remove env variable.
        putenv($key);

        // Remove $_ENV key.
        if (array_key_exists($key, $_ENV)) {
            unset($_ENV[$key]);
        }

        if ($value !== null) {
            $_SERVER[$key] = $value;

            return $this;
        }

        if (array_key_exists($key, $_SERVER)) {
            unset($_SERVER[$key]);
        }

        return $this;
    }
}

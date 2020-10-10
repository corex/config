<?php

declare(strict_types=1);

namespace Tests\CoRex\Config;

use CoRex\Config\Config;
use CoRex\Config\Exceptions\EnvironmentException;
use CoRex\Helpers\Obj;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\CoRex\Config\HelperClasses\ArrayStorage;

class ConfigTest extends TestCase
{
    /** @var Config */
    private $config;

    /** @var mixed[] */
    private $actor1 = ['firstname' => 'Sean', 'lastname' => 'Connery'];

    /** @var mixed[] */
    private $actor2 = ['firstname' => 'Roger', 'lastname' => 'Moore'];

    /**
     * Test has().
     *
     * @throws EnvironmentException
     */
    public function testHas(): void
    {
        $this->assertTrue($this->config->has('actors.actor1.firstname'));
        $this->assertFalse($this->config->has('this.does.not.exist'));
    }

    /**
     * Test get().
     *
     * @throws EnvironmentException
     */
    public function testGet(): void
    {
        $this->assertSame($this->actor1, $this->config->get('actors.actor1'));
    }

    /**
     * Test getString.
     *
     * @throws EnvironmentException
     */
    public function testGetString(): void
    {
        $this->assertSame('1', $this->config->getString('values.value_1'));
    }

    /**
     * Test getInt().
     *
     * @throws EnvironmentException
     */
    public function testGetInt(): void
    {
        $this->assertSame(1, $this->config->getInt('values.value_string_1'));
    }

    /**
     * Test getBool().
     *
     * @throws EnvironmentException
     */
    public function testGetBool(): void
    {
        $this->assertTrue($this->config->getBool('values.value_string_true'));
        $this->assertFalse($this->config->getBool('values.value_string_false'));
    }

    /**
     * Test getData() default.
     *
     * @throws ReflectionException
     */
    public function testGetDataDefault(): void
    {
        $this->assertSame('unknown', $this->getData('', 'unknown'));
    }

    /**
     * Test getData() from environment.
     *
     * @throws ReflectionException
     */
    public function testGetDataFromEnvironment(): void
    {
        $_SERVER['MY_KEY'] = 'my.value';
        $this->assertSame('my.value', $this->getData('my.key', 'unknown'));
    }

    /**
     * Test getData() not loaded.
     *
     * @throws ReflectionException
     */
    public function testGetDataNotLoaded(): void
    {
        $this->assertSame([], Obj::getProperty('data', $this->config));
        $this->assertSame($this->actor1['firstname'], $this->getData('actors.actor1.firstname', 'unknown'));
        $this->assertSame($this->actor1['lastname'], $this->getData('actors.actor1.lastname', 'unknown'));
        $this->assertNotSame([], Obj::getProperty('data', $this->config));
    }

    /**
     * Test getData() is array and return default.
     *
     * @throws ReflectionException
     */
    public function testGetDataIsArrayReturnDefault(): void
    {
        $this->assertSame('unknown', $this->getData('actors.actor1.firstname.davs', 'unknown'));
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $data = [
            'actors' => [
                'actor1' => $this->actor1,
                'actor2' => $this->actor2,
            ],
            'values' => [
                'value_1' => 1,
                'value_true' => true,
                'value_string_1' => '1',
                'value_string_true' => 'true',
                'value_string_yes' => 'yes',
                'value_string_on' => 'on',
                'value_0' => 0,
                'value_false' => false,
                'value_string_0' => '0',
                'value_string_false' => 'false',
                'value_string_no' => 'no',
                'value_string_off' => 'of',
            ]
        ];

        $storage = new ArrayStorage($data);
        $this->config = new Config($storage);
    }

    /**
     * Get data.
     *
     * @param string $key
     * @param null $default
     * @return mixed|mixed[]|null
     * @throws ReflectionException
     */
    private function getData(string $key, $default = null)
    {
        return Obj::callMethod('getData', $this->config, ['key' => $key, 'default' => $default]);
    }
}

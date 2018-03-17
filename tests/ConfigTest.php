<?php

namespace Tests\CoRex\Config;

use CoRex\Config\Config;
use CoRex\Config\ConfigException;
use CoRex\Config\Environment;
use CoRex\Config\Path;
use CoRex\Config\Repository;
use CoRex\Support\Obj;
use CoRex\Support\System\Directory;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Tests\CoRex\Config\Helpers\ConfigHelper;

class ConfigTest extends TestCase
{
    private $tempDirectory;

    private $actor1 = ['firstname' => 'Sean', 'lastname' => 'Connery'];
    private $actor2 = ['firstname' => 'Roger', 'lastname' => 'Moore'];
    private $actor3 = ['firstname' => 'Daniel', 'lastname' => 'Craig'];
    private $actor4 = ['firstname' => 'Pierce', 'lastname' => 'Brosnan'];

    private $app1 = 'app.1';
    private $app2 = 'app.2';

    /**
     * Test has.
     *
     * @throws ConfigException
     */
    public function testHas()
    {
        $this->prepareData([
            'actor1' => $this->actor1,
            'actor2' => $this->actor2
        ]);
        $this->assertTrue(Config::has('test.actor1'));
        $this->assertTrue(Config::has('test.actor2'));
        $this->assertFalse(Config::has('test.unknown'));
    }

    /**
     * Test get.
     *
     * @throws ConfigException
     */
    public function testGet()
    {
        $this->prepareData([
            'actor1' => $this->actor1,
            'actor2' => $this->actor2
        ]);
        $this->assertEquals($this->actor1, Config::get('test.actor1'));
        $this->assertEquals($this->actor2, Config::get('test.actor2'));
    }

    /**
     * Test getEnvironment.
     *
     * @throws ConfigException
     */
    public function testGetEnvironment()
    {
        $this->prepareData([
            'actor1' => $this->actor1,
            'actor2' => $this->actor2
        ]);
        $this->prepareData([
            'actor2' => $this->actor3,
            'actor3' => $this->actor4
        ], Environment::TESTING);

        $this->assertEquals($this->actor1, Config::get('test.actor1'));
        $this->assertEquals($this->actor3, Config::get('test.actor2'));
        $this->assertEquals($this->actor4, Config::get('test.actor3'));
    }

    /**
     * Test get int.
     *
     * @throws ConfigException
     */
    public function testGetInt()
    {
        $number1 = mt_rand(1, 100000);
        $number2 = mt_rand(1, 100000);
        $this->prepareData([
            'number1' => $number1,
            'number2' => $number2
        ]);
        $this->assertEquals($number1, Config::getInt('test.number1'));
        $this->assertEquals($number2, Config::getInt('test.number2'));
        $this->assertEquals(0, Config::getInt('test.unknown'));
    }

    /**
     * Test get bool.
     *
     * @throws ConfigException
     */
    public function testGetBool()
    {
        $bool1 = mt_rand(0, 1) == 1;
        $bool2 = mt_rand(0, 1) == 1;
        $this->initialize();
        $this->prepareData([
            'bool1' => $bool1,
            'bool2' => $bool2
        ]);
        $this->assertEquals($bool1, Config::getInt('test.bool1'));
        $this->assertEquals($bool2, Config::getInt('test.bool2'));
        $this->assertEquals(false, Config::getInt('test.unknown'));
    }

    /**
     * Test set.
     *
     * @throws ConfigException
     */
    public function testSet()
    {
        if (!Config::isAppRegistered()) {
            Config::registerApp($this->tempDirectory);
        }
        $repository = Config::repository();
        $this->assertEquals([], $repository->all());
        $check = md5(mt_rand(1, 100000));
        Config::set('test', $check);
        $this->assertEquals([
            'test' => $check
        ], $repository->all());
    }

    /**
     * Test remove.
     *
     * @throws ConfigException
     */
    public function testRemove()
    {
        $this->prepareData([
            'actor1' => $this->actor1,
            'actor2' => $this->actor2
        ]);

        $this->assertTrue(Config::has('test.actor1'));
        $this->assertTrue(Config::has('test.actor2'));

        Config::remove('test.actor1');

        $this->assertFalse(Config::has('test.actor1'));
        $this->assertTrue(Config::has('test.actor2'));
    }

    /**
     * Test all.
     *
     * @throws ConfigException
     */
    public function testAll()
    {
        $this->prepareData([
            'actor1' => $this->actor1,
            'actor2' => $this->actor2
        ]);
        $this->assertTrue(Config::has('test.actor1'));
        $this->assertTrue(Config::has('test.actor2'));
        $this->assertFalse(Config::has('test.unknown'));
    }

    /**
     * Test apps.
     * @throws ConfigException
     */
    public function testApps()
    {
        $this->testRegisterApp();
    }

    /**
     * Test register app.
     *
     * @throws ConfigException
     */
    public function testRegisterApp()
    {
        $pathApp1 = $this->tempDirectory . '/' . md5(mt_rand(1, 100000));
        Directory::make($pathApp1);

        $pathApp2 = $this->tempDirectory . '/' . md5(mt_rand(1, 100000));
        Directory::make($pathApp2);

        $pathApp3 = $this->tempDirectory . '/' . md5(mt_rand(1, 100000));
        Directory::make($pathApp3);

        // Register apps.
        if (!Config::isAppRegistered()) {
            Config::registerApp($pathApp1);
        }
        if (!Config::isAppRegistered($this->app1)) {
            Config::registerApp($pathApp2, $this->app1);
        }
        if (!Config::isAppRegistered($this->app2)) {
            Config::registerApp($pathApp3, $this->app2);
        }

        // Compare app registrations.
        $apps = Config::apps();
        $this->assertTrue(in_array('*', $apps));
        $this->assertTrue(in_array($this->app1, $apps));
        $this->assertTrue(in_array($this->app2, $apps));
        $this->assertEquals($pathApp1, Config::repository()->getPath());
        $this->assertEquals($pathApp2, Config::repository($this->app1)->getPath());
        $this->assertEquals($pathApp3, Config::repository($this->app2)->getPath());
    }

    /**
     * Test isAppRegistered.
     *
     * @throws ConfigException
     */
    public function testIsAppRegistered()
    {
        $pathApp1 = $this->tempDirectory . '/' . md5(mt_rand(1, 100000));
        Directory::make($pathApp1);

        $pathApp2 = $this->tempDirectory . '/' . md5(mt_rand(1, 100000));
        Directory::make($pathApp2);

        Config::registerApp($pathApp1);
        Config::registerApp($pathApp2, $this->app1);

        $this->assertTrue(Config::isAppRegistered());
        $this->assertTrue(Config::isAppRegistered($this->app1));
        $this->assertFalse(Config::isAppRegistered($this->app2));
    }

    /**
     * Test repository.
     *
     * @throws ConfigException
     */
    public function testRepository()
    {
        $pathApp1 = $this->tempDirectory . '/' . md5(mt_rand(1, 100000));
        Directory::make($pathApp1);
        Config::registerApp($pathApp1);
        $repository = COnfig::repository();
        $this->assertInstanceOf(Repository::class, $repository);
    }

    /**
     * Test env.
     */
    public function testEnv()
    {
        $this->assertEquals(Config::appEnvironment(), Config::env('APP_ENV'));
    }

    /**
     * Test envInt.
     */
    public function testEnvInt()
    {
        $this->assertEquals(4, Config::envInt('APP_INT'));
        $this->assertTrue(is_int(Config::envInt('APP_INT')));
    }

    /**
     * Test envBool.
     */
    public function testEnvBool()
    {
        $this->assertTrue(Config::envBool('APP_BOOL1'));
        $this->assertTrue(is_bool(Config::envBool('APP_BOOL1')));

        $this->assertTrue(Config::envBool('APP_BOOL_TRUE'));
        $this->assertTrue(is_bool(Config::envBool('APP_BOOL_TRUE')));

        $this->assertTrue(Config::envBool('APP_BOOL_YES'));
        $this->assertTrue(is_bool(Config::envBool('APP_BOOL_YES')));
    }

    /**
     * Test appEnvironment.
     */
    public function testAppEnvironment()
    {
        $this->assertEquals(Environment::TESTING, Config::appEnvironment());
    }

    /**
     * Test initialize.
     */
    public function testInitialize()
    {
        $this->clearConfig();

        $this->assertNull($this->getProperty('isLoaded'));
        $this->assertNull($this->getProperty('repositories'));
        $this->assertNull($this->getProperty('dotenv'));

        $this->initialize();

        $this->assertTrue($this->getProperty('isLoaded'));
        $this->assertEquals([], $this->getProperty('repositories'));
        $this->assertInstanceOf(Dotenv::class, $this->getProperty('dotenv'));
    }

    /**
     * Setup.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->tempDirectory = ConfigHelper::getUniquePath('corex-config-helper');
        $this->clearConfig();
        $this->initialize();
    }

    /**
     * Tear down.
     */
    protected function tearDown()
    {
        parent::tearDown();
//        Directory::delete($this->tempDirectory);
    }

    /**
     * Clear config.
     */
    private function clearConfig()
    {
        Obj::setProperty('isLoaded', null, null, Config::class);
        Obj::setProperty('repositories', null, null, Config::class);
        Obj::setProperty('dotenv', null, null, Config::class);
    }

    /**
     * Initialize.
     */
    private function initialize()
    {
        Config::initialize(Path::root('tests.Helpers'));
    }

    /**
     * Get property.
     *
     * @param string $property
     * @return mixed
     */
    private function getProperty($property)
    {
        return Obj::getProperty($property, null, null, Config::class);
    }

    /**
     * Prepare data.
     *
     * @param array $data
     * @param string $environment Default null which means none.
     * @throws ConfigException
     */
    private function prepareData(array $data, $environment = null)
    {
        if ($environment === null) {
            $environment = '';
        } elseif ($environment !== null) {
            $environment = '.' . $environment;
        }
        if (!Config::isAppRegistered()) {
            Config::registerApp($this->tempDirectory);
        }
        ConfigHelper::prepareConfigFiles($this->tempDirectory, 'test' . $environment, $data);
        Config::repository()->clear();
        Config::repository()->reload();
    }
}

<?php

declare(strict_types=1);

namespace Tests\CoRex\Config;

use CoRex\Config\Config;
use CoRex\Config\Environment;
use CoRex\Config\Exceptions\ConfigException;
use CoRex\Config\Path;
use CoRex\Config\Repository;
use CoRex\Filesystem\Directory;
use CoRex\Helpers\Obj;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Tests\CoRex\Config\Helpers\ConfigHelper;

class ConfigTest extends TestCase
{
    /** @var string */
    private $tempDirectory;

    /** @var mixed[] */
    private $actor1 = ['firstname' => 'Sean', 'lastname' => 'Connery'];

    /** @var mixed[] */
    private $actor2 = ['firstname' => 'Roger', 'lastname' => 'Moore'];

    /** @var mixed[] */
    private $actor3 = ['firstname' => 'Daniel', 'lastname' => 'Craig'];

    /** @var mixed[] */
    private $actor4 = ['firstname' => 'Pierce', 'lastname' => 'Brosnan'];

    /** @var string */
    private $app1 = 'app.1';

    /** @var string */
    private $app2 = 'app.2';

    /**
     * Test has.
     *
     * @throws ConfigException
     */
    public function testHas(): void
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
    public function testGet(): void
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
    public function testGetEnvironment(): void
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
    public function testGetInt(): void
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
    public function testGetBool(): void
    {
        $bool1 = mt_rand(0, 1) === 1;
        $bool2 = mt_rand(0, 1) === 1;
        $this->initialize();
        $this->prepareData([
            'bool1' => $bool1,
            'bool2' => $bool2
        ]);
        $this->assertEquals($bool1, Config::getBool('test.bool1'));
        $this->assertEquals($bool2, Config::getBool('test.bool2'));
        $this->assertEquals(false, Config::getBool('test.unknown'));
    }

    /**
     * Test set.
     *
     * @throws ConfigException
     */
    public function testSet(): void
    {
        if (!Config::isAppRegistered()) {
            Config::registerApp($this->tempDirectory);
        }
        $repository = Config::repository();
        $this->assertEquals([], $repository->all());
        $check = md5((string)mt_rand(1, 100000));
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
    public function testRemove(): void
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
    public function testAll(): void
    {
        $checkData = [
            'actor1' => $this->actor1,
            'actor2' => $this->actor2
        ];
        $this->prepareData($checkData);

        $this->assertEquals([
            'test' => $checkData
        ], Config::all());
    }

    /**
     * Test apps.
     *
     * @throws ConfigException
     */
    public function testApps(): void
    {
        $this->testRegisterApp();
    }

    /**
     * Test register app.
     *
     * @throws ConfigException
     */
    public function testRegisterApp(): void
    {
        $pathApp1 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp1);

        $pathApp2 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp2);

        $pathApp3 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
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
     * Test register app when already registered.
     *
     * @throws ConfigException
     */
    public function testRegisterAppWhenAlreadyRegistered(): void
    {
        $pathApp = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp);

        // Register apps.
        if (!Config::isAppRegistered()) {
            Config::registerApp($pathApp);
        }

        // Compare app registrations.
        $apps = Config::apps();
        $this->assertTrue(in_array('*', $apps));

        // Register second time to check exception.
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Application * already registered.');
        Config::registerApp($pathApp);
    }

    /**
     * Test register app not found.
     *
     * @throws ConfigException
     */
    public function testRegisterAppPathNotFound(): void
    {
        $pathApp = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Path ' . $pathApp . ' does not exist.');
        Config::registerApp($pathApp);
    }

    /**
     * Test isAppRegistered.
     *
     * @throws ConfigException
     */
    public function testIsAppRegistered(): void
    {
        $pathApp1 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp1);

        $pathApp2 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp2);

        Config::registerApp($pathApp1);
        Config::registerApp($pathApp2, $this->app1);

        $this->assertTrue(Config::isAppRegistered());
        $this->assertTrue(Config::isAppRegistered($this->app1));
        $this->assertFalse(Config::isAppRegistered($this->app2));
    }

    /**
     * Test unregisterApp.
     *
     * @throws ConfigException
     */
    public function testUnregisterApp(): void
    {
        $pathApp1 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp1);

        $pathApp2 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp2);

        Config::registerApp($pathApp1);
        Config::registerApp($pathApp2, $this->app1);

        $this->assertTrue(Config::isAppRegistered());
        $this->assertTrue(Config::isAppRegistered($this->app1));
        $this->assertFalse(Config::isAppRegistered($this->app2));

        // Unregister and check default app.
        Config::unregisterApp();
        $this->assertFalse(Config::isAppRegistered());
        $this->assertTrue(Config::isAppRegistered($this->app1));
        $this->assertFalse(Config::isAppRegistered($this->app2));

        // Unregister and check app 1.
        Config::unregisterApp($this->app1);
        $this->assertFalse(Config::isAppRegistered());
        $this->assertFalse(Config::isAppRegistered($this->app1));
        $this->assertFalse(Config::isAppRegistered($this->app2));
    }

    /**
     * Test repository.
     *
     * @throws ConfigException
     */
    public function testRepository(): void
    {
        $pathApp1 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp1);
        Config::registerApp($pathApp1);
        $repository = Config::repository();
        $this->assertInstanceOf(Repository::class, $repository);
    }

    /**
     * Test repository path does not exist.
     *
     * @throws ConfigException
     */
    public function testRepositoryPathDoesNotExist(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Path ' . Path::root('config') . ' does not exist.');
        Config::repository();
    }

    /**
     * Test repository path exist.
     *
     * @throws ConfigException
     */
    public function testRepositoryPathExist(): void
    {
        $path = Path::root('config');
        Directory::make($path);
        Config::repository();
        $repository = Config::repository();
        $this->assertInstanceOf(Repository::class, $repository);
        $this->assertEquals($path, $repository->getPath());
    }

    /**
     * Test repository unknown not registered.
     *
     * @throws ConfigException
     */
    public function testRepositoryUnknownNotRegistered(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Application unknown not registered.');
        Config::repository('unknown');
    }

    /**
     * Test env.
     */
    public function testEnv(): void
    {
        $this->assertEquals(Config::appEnvironment(), Config::env('APP_ENV'));
    }

    /**
     * Test env default.
     */
    public function testEnvDefault(): void
    {
        $check = md5((string)mt_rand(1, 100000));
        $this->assertEquals($check, Config::env('unknown', $check));
    }

    /**
     * Test envInt.
     */
    public function testEnvInt(): void
    {
        $this->assertEquals(4, Config::envInt('APP_INT'));
        $this->assertTrue(is_int(Config::envInt('APP_INT')));
    }

    /**
     * Test envBool.
     */
    public function testEnvBool(): void
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
    public function testAppEnvironment(): void
    {
        $this->assertEquals(Environment::TESTING, Config::appEnvironment());
    }

    /**
     * Test appPath.
     *
     * @throws ConfigException
     */
    public function testAppPath(): void
    {
        $pathApp1 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp1);

        $pathApp2 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
        Directory::make($pathApp2);

        $pathApp3 = $this->tempDirectory . '/' . md5((string)mt_rand(1, 100000));
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

        // Check app paths.
        $this->assertEquals($pathApp1, Config::appPath());
        $this->assertEquals($pathApp2, Config::appPath($this->app1));
        $this->assertEquals($pathApp3, Config::appPath($this->app2));
        $this->assertNull(Config::appPath('unknown'));
    }

    /**
     * Test initialize.
     *
     * @throws \ReflectionException
     */
    public function testInitialize(): void
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
     * Test inialize path null.
     *
     * @throws \ReflectionException
     */
    public function testInitializePathNull(): void
    {
        $this->clearConfig();

        $this->assertNull($this->getProperty('isLoaded'));
        $this->assertNull($this->getProperty('repositories'));
        $this->assertNull($this->getProperty('dotenv'));

        Config::initialize();

        $this->assertEquals(dirname(__DIR__), Obj::getProperty('path', null, null, Config::class));
    }

    /**
     * Setup.
     *
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDirectory = ConfigHelper::getUniquePath('corex-config-helper');
        $this->clearConfig();
        $this->initialize();
    }

    /**
     * Tear down.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Directory::delete($this->tempDirectory);
        Directory::delete(Path::root('config'));
    }

    /**
     * Clear config.
     *
     * @throws \ReflectionException
     */
    private function clearConfig(): void
    {
        Obj::setProperty('isLoaded', null, null, Config::class);
        Obj::setProperty('repositories', null, null, Config::class);
        Obj::setProperty('dotenv', null, null, Config::class);
    }

    /**
     * Initialize.
     */
    private function initialize(): void
    {
        Config::initialize(Path::root('tests.Helpers'));
    }

    /**
     * Get property.
     *
     * @param string $property
     * @return mixed
     * @throws \ReflectionException
     */
    private function getProperty(string $property)
    {
        return Obj::getProperty($property, null, null, Config::class);
    }

    /**
     * Prepare data.
     *
     * @param mixed[] $data
     * @param string $environment Default null which means none.
     * @throws ConfigException
     */
    private function prepareData(array $data, ?string $environment = null): void
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

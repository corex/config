<?php

namespace Tests\CoRex\Config;

use CoRex\Config\Config;
use CoRex\Config\ConfigException;
use CoRex\Config\Environment;
use CoRex\Config\Path;
use CoRex\Config\Repository;
use CoRex\Support\Obj;
use CoRex\Support\System\Directory;
use PHPUnit\Framework\TestCase;
use Tests\CoRex\Config\Helpers\ConfigHelper;

class RepositoryTest extends TestCase
{
    private $tempDirectory;

    private $actor1 = ['firstname' => 'Sean', 'lastname' => 'Connery'];
    private $actor2 = ['firstname' => 'Roger', 'lastname' => 'Moore'];

    /**
     * Test constructor.
     *
     * @throws ConfigException
     */
    public function testConstructor()
    {
        $repository = new Repository($this->tempDirectory);
        $this->assertEquals($this->tempDirectory, Obj::getProperty('path', $repository));
        $this->assertEquals(Environment::environments(), Obj::getProperty('environments', $repository));
        $this->assertEquals(Environment::TESTING, Obj::getProperty('environment', $repository));
        $this->assertEquals([], Obj::getProperty('items', $repository));
    }

    /**
     * Test constructor environment not supported.
     */
    public function testConstructorEnvironmentNotSupported()
    {
        putenv('APP_ENV=unknown');
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Environment unknown not supported.');
        new Repository($this->tempDirectory);
    }

    /**
     * Test clear.
     *
     * @throws ConfigException
     */
    public function testClear()
    {
        $check = md5(mt_rand(1, 100000));
        $repository = new Repository($this->tempDirectory);
        $repository->set('this.is.a.test', $check);
        $this->assertNotEquals([], $repository->all());
        $repository->clear();
        $this->assertEquals([], $repository->all());
    }

    /**
     * Test getPath.
     *
     * @throws ConfigException
     */
    public function testGetPath()
    {
        $repository = new Repository($this->tempDirectory);
        $this->assertEquals($this->tempDirectory, $repository->getPath());
    }

    /**
     * Test has.
     *
     * @throws ConfigException
     */
    public function testHas()
    {
        $check = md5(mt_rand(1, 100000));
        $repository = new Repository($this->tempDirectory);
        $this->assertFalse($repository->has('this.is.a.test'));
        $repository->set('this.is.a.test', $check);
        $this->assertTrue($repository->has('this.is.a.test'));
    }

    /**
     * Test get.
     *
     * @throws ConfigException
     */
    public function testGet()
    {
        $check = md5(mt_rand(1, 100000));
        $repository = new Repository($this->tempDirectory);
        $this->assertNull($repository->get('this.is.a.test'));
        $repository->set('this.is.a.test', $check);
        $this->assertEquals($check, $repository->get('this.is.a.test'));
    }

    /**
     * Test getInt.
     *
     * @throws ConfigException
     */
    public function testGetInt()
    {
        $check = mt_rand(1, 100000);
        $repository = new Repository($this->tempDirectory);
        $this->assertEquals(0, $repository->getInt('this.is.a.test'));
        $repository->set('this.is.a.test', $check);
        $this->assertEquals($check, $repository->getInt('this.is.a.test'));
    }

    /**
     * Test getBool.
     *
     * @throws ConfigException
     */
    public function testGetBool()
    {
        $repository = new Repository($this->tempDirectory);
        $this->assertFalse($repository->getBool('this.is.a.test'));
        $repository->set('this.is.a.test', false);
        $this->assertFalse($repository->getBool('this.is.a.test'));
        $repository->set('this.is.a.test', true);
        $this->assertTrue($repository->getBool('this.is.a.test'));
        $repository->set('this.is.a.test', 'true');
        $this->assertTrue($repository->getBool('this.is.a.test'));
    }

    /**
     * Test set.
     *
     * @throws ConfigException
     */
    public function testSet()
    {
        $this->testHas();
    }

    /**
     * Test remove.
     *
     * @throws ConfigException
     * @throws \Exception
     */
    public function testRemove()
    {
        $check = md5(mt_rand(1, 100000));
        $repository = new Repository($this->tempDirectory);
        $this->assertNull($repository->get('this.is.a.test'));
        $repository->set('this.is.a.test', $check);
        $this->assertEquals($check, $repository->get('this.is.a.test'));
        $repository->remove('this.is.a.test');
        $this->assertNull($repository->get('this.is.a.test'));
    }

    /**
     * Test all.
     *
     * @throws ConfigException
     */
    public function testAll()
    {
        $check = md5(mt_rand(1, 100000));
        $repository = new Repository($this->tempDirectory);
        $this->assertNull($repository->get('this.is.a.test'));
        $repository->set('this.is.a.test', $check);
        $this->assertEquals([
            'this' => [
                'is' => [
                    'a' => [
                        'test' => $check
                    ]
                ]
            ]
        ], $repository->all());
    }

    /**
     * Test loadFiles/loadFile.
     *
     * @throws ConfigException
     */
    public function testLoadFilesAndLoadFile()
    {
        ConfigHelper::prepareConfigFiles($this->tempDirectory, 'test1', [
            'actor1' => $this->actor1
        ]);
        ConfigHelper::prepareConfigFiles($this->tempDirectory, 'test2', [
            'actor2' => $this->actor2
        ]);
        $repository = new Repository($this->tempDirectory);
        $this->assertEquals(['actor1' => $this->actor1], $repository->get('test1'));
        $this->assertEquals(['actor2' => $this->actor2], $repository->get('test2'));
    }

    /**
     * Test load files failed path.
     */
    public function testLoadFilesFailedPath()
    {
        Directory::delete($this->tempDirectory);
        $repository = new Repository($this->tempDirectory);
        $this->assertEquals([], $repository->all());
    }

    /**
     * Test load file path relative.
     */
    public function testLoadFilePathRelative()
    {
        $items = [];
        $repository = new Repository($this->tempDirectory);
        $configPath = Path::packageCurrent(['tests', 'files']);
        Obj::callMethod('loadFile', $repository, [
            'items' => &$items,
            'path' => $configPath,
            'pathRelative' => 'sub',
            'configKey' => 'test'
        ]);
        $this->assertEquals([
            'sub' => [
                'test' => [
                    'this' => [
                        'is' => [
                            'a' => [
                                'test' => 'something'
                            ]
                        ]
                    ]
                ]
            ]
        ], $items);
    }

    /**
     * Test isEnvironmentFilename.
     *
     * @throws ConfigException
     */
    public function testIsEnvironmentFilename()
    {
        $repository = new Repository($this->tempDirectory);

        // Check standard config-file.
        $this->assertFalse(Obj::callMethod('isEnvironmentFilename', $repository, ['test.php']));

        // Check all environments.
        $enviroments = Environment::environments();
        foreach ($enviroments as $enviroment) {
            $this->assertTrue(Obj::callMethod('isEnvironmentFilename', $repository, ['test.' . $enviroment . '.php']));
        }
    }

    /**
     * Setup.
     */
    protected function setUp()
    {
        parent::setUp();
        Config::initialize(Path::root('tests.Helpers'));
        $this->tempDirectory = ConfigHelper::getUniquePath('corex-config-helper');

        // Set testing mode.
        putenv('APP_ENV=testing');
    }

    /**
     * Tear down.
     */
    protected function tearDown()
    {
        parent::tearDown();
        Directory::delete($this->tempDirectory);
    }
}

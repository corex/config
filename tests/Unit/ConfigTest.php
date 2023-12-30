<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit;

use CoRex\Config\Adapter\AbstractAdapter;
use CoRex\Config\Adapter\AdapterInterface;
use CoRex\Config\Adapter\ArrayAdapter;
use CoRex\Config\Config;
use CoRex\Config\ConfigInterface;
use CoRex\Config\Data\TrueFalse;
use CoRex\Config\Data\Value;
use CoRex\Config\Exceptions\AdapterException;
use CoRex\Config\Exceptions\ConfigException;
use CoRex\Config\Exceptions\TypeException;
use CoRex\Config\Key\Key;
use CoRex\Config\Key\KeyInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \CoRex\Config\Config
 */
class ConfigTest extends TestCase
{
    /** @var array<string, array<string, array<int|string, int|string>|bool|float|int|string|null>> */

// array<string, array<string, array<int|string, int|string>|bool|float|int|string|null>>>

    private array $data = [];

    private AdapterInterface $adapter;
    private ConfigInterface $config;

    public function testConstructorWhenNoAdapters(): void
    {
        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage('No adapters specified.');

        new Config([]);
    }

    public function testConstructorWhenNoAdapter(): void
    {
        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage('Adapter specified is not an adapter.');

        new Config(['not-an-object']); // @phpstan-ignore-line
    }

    public function testConstructorWhenNotImplementingCorrectInterface(): void
    {
        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Adapter "%s" does not implement %s.',
                'stdClass',
                AdapterInterface::class
            )
        );

        new Config([new stdClass()]); // @phpstan-ignore-line
    }

    public function testConstructorWhenAdapterAlreadyLoaded(): void
    {
        $adapter = new class extends AbstractAdapter {
            /**
             * @inheritDoc
             */
            public function getValue(KeyInterface $key): Value
            {
                return new Value(null, $key, null);
            }
        };

        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Adapter "%s" already loaded.',
                get_class($adapter)
            )
        );

        new Config([$adapter, $adapter]);
    }

    public function testHas(): void
    {
        $this->assertTrue($this->config->has('actor1'));
        $this->assertFalse($this->config->has('unknown.key'));
    }

    public function testGetValueObjectWorks(): void
    {
        $valueObject = $this->config->getValueObject('actor1.firstname');

        $this->assertSame($this->adapter, $valueObject->getAdapter());
        $this->assertTrue($valueObject->hasKey());
        $this->assertEquals(new Key('actor1.firstname'), $valueObject->getKey());
        $this->assertSame($this->data['actor1']['firstname'], $valueObject->getValue());
    }

    public function testGetValueObjectUnknownKey(): void
    {
        $valueObject = $this->config->getValueObject('unknown.key');

        $this->assertNull($valueObject->getAdapter());
        $this->assertFalse($valueObject->hasKey());
        $this->assertEquals(new Key('unknown.key'), $valueObject->getKey());
        $this->assertNull($valueObject->getValue());
    }

    public function testGetMixedOrNullWorks(): void
    {
        $this->assertSame(
            $this->data['actor1']['firstname'],
            $this->config->getMixedOrNull('actor1.firstname')
        );

        $this->assertNull($this->config->getMixedOrNull('unknown.key'));
    }

    public function testGetMixedWorks(): void
    {
        $this->assertSame(
            $this->data['actor1']['firstname'],
            $this->config->getMixed('actor1.firstname')
        );
    }

    public function testGetMixedFailed(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getMixed'
            )
        );

        $this->config->getMixed('unknown.key');
    }

    public function testGetStringOrNullWorks(): void
    {
        $this->assertSame(
            $this->data['actor1']['firstname'],
            $this->config->getStringOrNull('actor1.firstname')
        );

        $this->assertNull($this->config->getStringOrNull('unknown.key'));
    }

    public function testGetStringOrNullWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" or null' .
                ' when using "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'string',
                'getStringOrNull',
                ArrayAdapter::class
            )
        );

        $this->config->getStringOrNull('values.value_1');
    }

    public function testGetStringWorks(): void
    {
        $this->assertSame(
            $this->data['actor1']['firstname'],
            $this->config->getString('actor1.firstname')
        );
    }

    public function testGetStringWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'string',
                'getString',
                ArrayAdapter::class
            )
        );

        $this->config->getString('values.value_1');
    }

    public function testGetStringWhenNull(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_null',
                'null',
                'string',
                'getString',
                ArrayAdapter::class
            )
        );

        $this->config->getString('values.value_null');
    }

    public function testGetStringWhenNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getString'
            )
        );

        $this->config->getString('unknown.key');
    }

    public function testGetIntOrNullWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_1'],
            $this->config->getIntOrNull('values.value_1')
        );

        $this->assertNull($this->config->getIntOrNull('unknown.key'));
    }

    public function testGetIntOrNullWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" or null' .
                ' when using "%s()" to get data via adapter "%s".',
                'values.value_string_true',
                'string',
                'integer',
                'getIntOrNull',
                ArrayAdapter::class
            )
        );

        $this->config->getIntOrNull('values.value_string_true');
    }

    public function testGetIntWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_1'],
            $this->config->getInt('values.value_1')
        );
    }

    public function testGetIntWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_string_true',
                'string',
                'integer',
                'getInt',
                ArrayAdapter::class
            )
        );

        $this->config->getInt('values.value_string_true');
    }

    public function testGetIntWhenNull(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_null',
                'null',
                'integer',
                'getInt',
                ArrayAdapter::class
            )
        );

        $this->config->getInt('values.value_null');
    }

    public function testGetIntWhenNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getInt'
            )
        );

        $this->config->getInt('unknown.key');
    }

    public function testGetBoolOrNullWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_true'],
            $this->config->getBoolOrNull('values.value_true')
        );

        $this->assertNull($this->config->getBoolOrNull('unknown.key'));
    }

    public function testGetBoolOrNullWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" or null' .
                ' when using "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'boolean',
                'getBoolOrNull',
                ArrayAdapter::class
            )
        );

        $this->config->getBoolOrNull('values.value_1');
    }

    public function testGetBoolWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_true'],
            $this->config->getBool('values.value_true')
        );
    }

    public function testGetBoolWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'boolean',
                'getBool',
                ArrayAdapter::class
            )
        );

        $this->config->getBool('values.value_1');
    }

    public function testGetBoolWhenNull(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_null',
                'null',
                'boolean',
                'getBool',
                ArrayAdapter::class
            )
        );

        $this->config->getBool('values.value_null');
    }

    public function testGetBoolWhenNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getBool'
            )
        );

        $this->config->getBool('unknown.key');
    }

    public function testGetTranslatedBoolOrNullWorks(): void
    {
        $this->assertTrue($this->config->getTranslatedBoolOrNull('values.value_1'));
        $this->assertTrue($this->config->getTranslatedBoolOrNull('values.value_true'));
        $this->assertTrue($this->config->getTranslatedBoolOrNull('values.value_string_1'));
        $this->assertTrue($this->config->getTranslatedBoolOrNull('values.value_string_true'));
        $this->assertTrue($this->config->getTranslatedBoolOrNull('values.value_string_yes'));
        $this->assertTrue($this->config->getTranslatedBoolOrNull('values.value_string_on'));

        $this->assertFalse($this->config->getTranslatedBoolOrNull('values.value_0'));
        $this->assertFalse($this->config->getTranslatedBoolOrNull('values.value_false'));
        $this->assertFalse($this->config->getTranslatedBoolOrNull('values.value_string_0'));
        $this->assertFalse($this->config->getTranslatedBoolOrNull('values.value_string_false'));
        $this->assertFalse($this->config->getTranslatedBoolOrNull('values.value_string_no'));
        $this->assertFalse($this->config->getTranslatedBoolOrNull('values.value_string_off'));

        $this->assertNull($this->config->getTranslatedBoolOrNull('unknown.key'));
    }

    public function testGetTranslatedBoolOrNullWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Value must be one of %s' .
                ' or null when using "%s()" to get data via adapter "%s".',
                'values.value_list',
                'array',
                implode(', ', TrueFalse::getReadableTrueFalseValues()),
                'getTranslatedBoolOrNull',
                ArrayAdapter::class
            )
        );

        $this->config->getTranslatedBoolOrNull('values.value_list');
    }

    public function testGetTranslatedBoolWorks(): void
    {
        $this->assertTrue($this->config->getTranslatedBool('values.value_1'));
    }

    public function testGetTranslatedBoolWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Value must be one of %s' .
                ' when using "%s()" to get data via adapter "%s".',
                'values.value_list',
                'array',
                implode(', ', TrueFalse::getReadableTrueFalseValues()),
                'getTranslatedBool',
                ArrayAdapter::class
            )
        );

        $this->config->getTranslatedBool('values.value_list');
    }

    public function testGetTranslatedBoolWhenNull(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Value must be one of %s' .
                ' when using "%s()" to get data via adapter "%s".',
                'values.value_null',
                'null',
                implode(', ', TrueFalse::getReadableTrueFalseValues()),
                'getTranslatedBool',
                ArrayAdapter::class
            )
        );

        $this->config->getTranslatedBool('values.value_null');
    }

    public function testGetTranslatedBoolWhenNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getTranslatedBool'
            )
        );

        $this->config->getTranslatedBool('unknown.key');
    }

    public function testGetDoubleOrNullWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_double'],
            $this->config->getDouble('values.value_double')
        );

        $this->assertNull($this->config->getDoubleOrNull('unknown.key'));
    }

    public function testGetDoubleOrNullWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" or null' .
                ' when using "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'double',
                'getDoubleOrNull',
                ArrayAdapter::class
            )
        );

        $this->config->getDoubleOrNull('values.value_1');
    }

    public function testGetDoubleWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_double'],
            $this->config->getDouble('values.value_double')
        );
    }

    public function testGetDoubleWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'double',
                'getDouble',
                ArrayAdapter::class
            )
        );

        $this->config->getDouble('values.value_1');
    }

    public function testGetDoubleWhenNull(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_null',
                'null',
                'double',
                'getDouble',
                ArrayAdapter::class
            )
        );

        $this->config->getDouble('values.value_null');
    }

    public function testGetDoubleWhenNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getDouble'
            )
        );

        $this->config->getDouble('unknown.key');
    }

    public function testGetArrayOrNullWorks(): void
    {
        $this->assertSame(
            $this->data['values'],
            $this->config->getArrayOrNull('values')
        );

        $this->assertNull($this->config->getStringOrNull('unknown.key'));
    }

    public function testGetArrayOrNullWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" or null' .
                ' when using "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'array',
                'getArrayOrNull',
                ArrayAdapter::class
            )
        );

        $this->config->getArrayOrNull('values.value_1');
    }

    public function testGetArrayWorks(): void
    {
        $this->assertSame(
            $this->data['values'],
            $this->config->getArray('values')
        );
    }

    public function testGetArrayWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'array',
                'getArray',
                ArrayAdapter::class
            )
        );

        $this->config->getArray('values.value_1');
    }

    public function testGetArrayWhenNull(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_null',
                'null',
                'array',
                'getArray',
                ArrayAdapter::class
            )
        );

        $this->config->getArray('values.value_null');
    }

    public function testGetArrayWhenNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getArray'
            )
        );

        $this->config->getArray('unknown.key');
    }

    public function testGetListOrNullWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_list'],
            $this->config->getListOrNull('values.value_list')
        );

        $this->assertNull($this->config->getListOrNull('unknown.key'));
    }

    public function testGetListOrNullWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" is an array, but not considered a list since its' .
                ' keys does not consist of consecutive numbers from 0 to count($array) - 1,' .
                ' when using "%s()" to get data via adapter "%s".',
                'values',
                'getListOrNull',
                ArrayAdapter::class
            )
        );

        $this->config->getListOrNull('values');
    }

    public function testGetListWorks(): void
    {
        $this->assertSame(
            $this->data['values']['value_list'],
            $this->config->getList('values.value_list')
        );
    }

    public function testGetListWhenWrongType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_1',
                'integer',
                'array',
                'getList',
                ArrayAdapter::class
            )
        );

        $this->config->getList('values.value_1');
    }

    public function testGetListWhenNotList(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" is an array, but not considered a list since its' .
                ' keys does not consist of consecutive numbers from 0 to count($array) - 1,' .
                ' when using "%s()" to get data via adapter "%s".',
                'values',
                'getList',
                ArrayAdapter::class
            )
        );

        $this->config->getList('values');
    }

    public function testGetListWhenNull(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" has type "%s". Must be "%s" when using' .
                ' "%s()" to get data via adapter "%s".',
                'values.value_null',
                'null',
                'array',
                'getList',
                ArrayAdapter::class
            )
        );

        $this->config->getList('values.value_null');
    }

    public function testGetListWhenNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value for key "%s" does not exist when using "%s()".',
                'unknown.key',
                'getList'
            )
        );

        $this->config->getList('unknown.key');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'actor1' => [
                'firstname' => 'Sean',
                'lastname' => 'Connery'
            ],
            'actor2' => [
                'firstname' => 'Roger',
                'lastname' => 'Moore'
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
                'value_string_off' => 'off',
                'value_null' => null,
                'value_double' => 10.4,
                'value_list' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0],
            ],
        ];

        $this->adapter = new ArrayAdapter($this->data);

        $this->config = new Config([$this->adapter]);
    }
}

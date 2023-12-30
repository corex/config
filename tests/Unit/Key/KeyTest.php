<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Key;

use CoRex\Config\Exceptions\KeyException;
use CoRex\Config\Key\Key;
use CoRex\Config\Key\KeyInterface;
use CoRex\Config\Key\KeyType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Key\Key
 */
class KeyTest extends TestCase
{
    private KeyInterface $key;

    public function testConstructorWhenEmptyKey(): void
    {
        $this->expectException(KeyException::class);
        $this->expectExceptionMessage('Keys must not be empty.');

        new Key(KeyType::MIXED, '');
    }

    public function testConstructorWhenKeyStartsWithDot(): void
    {
        $this->expectException(KeyException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Key "%s" must not start with a dot ".".',
                '.key'
            )
        );

        new Key(KeyType::MIXED, '.key');
    }

    public function testConstructorWhenKeyEndsWithDot(): void
    {
        $this->expectException(KeyException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Key "%s" must not end with a dot ".".',
                'key.'
            )
        );

        new Key(KeyType::MIXED, 'key.');
    }

    public function testConstructorWhenKeyFormatIsValid(): void
    {
        $keys = [
            'valid.key.with.dots',
            'valid.key.with.dots.12345678',
            'key-without_dots',
            'a.valid.key',
        ];

        foreach ($keys as $key) {
            $this->assertSame(
                $key,
                (new Key(KeyType::MIXED, $key))->getDotNotation()
            );
        }
    }

    public function testConstructorWhenKeyFormatIsInvalid(): void
    {
        $this->expectException(KeyException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Key format for key "%s" is not valid. It must be specified' .
                ' in simple words separated but a dot ".".',
                'invalid|key'
            )
        );

        new Key(KeyType::MIXED, 'invalid|key');
    }

    public function testGetSection(): void
    {
        $this->assertSame('bond', $this->key->getSection());
    }

    public function testGetKeyTypes(): void
    {
        foreach (KeyType::cases() as $case) {
            $key = new Key($case, 'something');
            $this->assertSame($case, $key->getKeyType());
        }
    }

    public function testGetParts(): void
    {
        $this->assertSame(['bond', 'actor', 'name'], $this->key->getParts(false));
        $this->assertSame(['actor', 'name'], $this->key->getParts(true));
    }

    public function testGetCustom(): void
    {
        $this->assertSame('bond|actor|name', $this->key->getCustom('|', false));
        $this->assertSame('actor|name', $this->key->getCustom('|', true));
    }

    public function testGetDotNotation(): void
    {
        $this->assertSame('bond.actor.name', $this->key->getDotNotation());
    }

    public function testGetShoutCase(): void
    {
        $this->assertSame('BOND_ACTOR_NAME', $this->key->getShoutCase());
    }

    public function testGetSnakeCase(): void
    {
        $this->assertSame('bond_actor_name', $this->key->getSnakeCase());
    }

    public function testGetKebabCase(): void
    {
        $this->assertSame('bond-actor-name', $this->key->getKebabCase());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key = new Key(KeyType::MIXED, 'bond.actor.name');
    }
}
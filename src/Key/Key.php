<?php

declare(strict_types=1);

namespace CoRex\Config\Key;

use CoRex\Config\Exceptions\KeyException;

/**
 * The basic format for key is dot annotation e.g. "my.config.var"
 * where "my" is the section of the key. Format is always dot notation.
 */
class Key implements KeyInterface
{
    private KeyType $keyType;

    /** @var array<string> */
    private array $parts;

    public function __construct(KeyType $keyType, string $key)
    {
        $this->keyType = $keyType;

        $parts = trim($key) !== '' ? explode('.', $key) : [];

        if (count($parts) === 0) {
            throw new KeyException('Keys must not be empty.');
        }

        if (str_starts_with($key, '.')) {
            throw new KeyException(
                sprintf(
                    'Key "%s" must not start with a dot ".".',
                    $key
                )
            );
        }

        if (str_ends_with($key, '.')) {
            throw new KeyException(
                sprintf(
                    'Key "%s" must not end with a dot ".".',
                    $key
                )
            );
        }

        if (!preg_match('/^[a-z0-9\-\._]+$/', $key)) {
            throw new KeyException(
                sprintf(
                    'Key format for key "%s" is not valid. It must be specified' .
                    ' in simple words separated but a dot ".".',
                    $key
                )
            );
        }

        $this->parts = $parts;
    }

    /**
     * @inheritDoc
     */
    public function getSection(): string
    {
        return $this->parts[0];
    }

    /**
     * Get key type.
     *
     * @return KeyType
     */
    public function getKeyType(): KeyType
    {
        return $this->keyType;
    }

    /**
     * @inheritDoc
     */
    public function getParts(bool $excludeSection): array
    {
        $parts = $this->parts;
        if ($excludeSection) {
            array_shift($parts);
        }

        return $parts;
    }

    /**
     * @inheritDoc
     */
    public function getCustom(string $separator, bool $excludeSection): string
    {
        return $this->buildKey($excludeSection, $separator);
    }

    /**
     * @inheritDoc
     */
    public function getDotNotation(): string
    {
        return $this->buildKey(false, '.');
    }

    /**
     * @inheritDoc
     */
    public function getShoutCase(): string
    {
        return strtoupper($this->buildKey(false, '_'));
    }

    /**
     * @inheritDoc
     */
    public function getSnakeCase(): string
    {
        return strtolower($this->buildKey(false, '_'));
    }

    /**
     * @inheritDoc
     */
    public function getKebabCase(): string
    {
        return strtolower($this->buildKey(false, '-'));
    }

    /**
     * Build key.
     *
     * @param bool $excludeSection
     * @param string|null $separator
     * @return string
     */
    private function buildKey(bool $excludeSection, ?string $separator): string
    {
        $parts = $this->getParts($excludeSection);

        return implode($separator ?? '', $parts);
    }
}
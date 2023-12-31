<?php

declare(strict_types=1);

namespace CoRex\Config\Section;

use CoRex\Config\ConfigInterface;

class Section implements SectionInterface
{
    private ConfigInterface $config;
    private string $section;

    public function __construct(ConfigInterface $config, string $section)
    {
        $this->config = $config;
        $this->section = $section;
    }

    /**
     * @inheritDoc
     */
    public function getSection(): string
    {
        return $this->section;
    }

    /**
     * @inheritDoc
     */
    public function has(string $configKey): bool
    {
        return $this->config->has($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getMixed(string $configKey): mixed
    {
        return $this->config->getMixed($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getStringOrNull(string $configKey): ?string
    {
        return $this->config->getStringOrNull($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getString(string $configKey): string
    {
        return $this->config->getString($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getIntOrNull(string $configKey): ?int
    {
        return $this->config->getIntOrNull($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getInt(string $configKey): int
    {
        return $this->config->getInt($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getBoolOrNull(string $configKey): ?bool
    {
        return $this->config->getBoolOrNull($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getBool(string $configKey): bool
    {
        return $this->config->getBool($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getTranslatedBoolOrNull(string $configKey): ?bool
    {
        return $this->config->getTranslatedBoolOrNull($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getTranslatedBool(string $configKey): bool
    {
        return $this->config->getTranslatedBool($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getDoubleOrNull(string $configKey): ?float
    {
        return $this->config->getDoubleOrNull($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getDouble(string $configKey): float
    {
        return $this->config->getDouble($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getArrayOrNull(string $configKey): ?array
    {
        return $this->config->getArrayOrNull($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getArray(string $configKey): array
    {
        return $this->config->getArray($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getListOrNull(string $configKey): ?array
    {
        return $this->config->getListOrNull($this->section . '.' . $configKey);
    }

    /**
     * @inheritDoc
     */
    public function getList(string $configKey): array
    {
        return $this->config->getList($this->section . '.' . $configKey);
    }
}
<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Filesystem\FilesystemInterface;
use CoRex\Config\Data\Data;
use CoRex\Config\Key\KeyInterface;
use CoRex\Config\Data\Value;

/**
 * This adapter handles array files for specified path.
 */
class ArrayFileAdapter extends AbstractAdapter
{
    private FilesystemInterface $filesystem;

    private string $pathToArrayFiles;

    /** @var array<int|string, mixed> */
    private array $data = [];

    private bool $isLoaded = false;

    public function __construct(FilesystemInterface $filesystem, string $pathToPhpArrayFiles)
    {
        $this->filesystem = $filesystem;
        $this->pathToArrayFiles = rtrim($pathToPhpArrayFiles, '/');
    }

    /**
     * @inheritDoc
     */
    public function getValue(KeyInterface $key): Value
    {
        $filename = $this->pathToArrayFiles . '/' . $key->getSection() . '.php';

        $this->data = [];
        if (!$this->isLoaded && $this->filesystem->fileExists($filename)) {
            $this->data = $this->filesystem->requireFileArray($filename);
            $this->isLoaded = true;
        }

        $configValue = Data::getFromArray($this->data, $key->getParts(true));

        return new Value(
            $configValue !== Data::NO_VALUE_FOUND_CODE ? $this : null,
            $key,
            $configValue !== Data::NO_VALUE_FOUND_CODE ? $configValue : null
        );
    }
}
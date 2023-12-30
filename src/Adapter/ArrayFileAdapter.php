<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Data\Data;
use CoRex\Config\Data\Value;
use CoRex\Config\Filesystem\FilesystemInterface;
use CoRex\Config\Key\KeyInterface;

/**
 * This adapter handles array files for specified path.
 */
class ArrayFileAdapter extends AbstractAdapter
{
    private FilesystemInterface $filesystem;

    private string $pathToArrayFiles;

    /** @var array<int|string, array<int|string, mixed>> */
    private array $data = [];

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
        $section = $key->getSection();

        $filename = $this->pathToArrayFiles . '/' . $section . '.php';

        if (!array_key_exists($section, $this->data)) {
            $this->data[$section] = [];
        }

        if ($this->filesystem->fileExists($filename)) {
            $this->data[$section] = $this->filesystem->requireFileArray($filename);
        }

        $configValue = Data::getFromArray($this->data[$section] ?? [], $key->getParts(true));

        return new Value(
            $configValue !== Data::NO_VALUE_FOUND_CODE ? $this : null,
            $key,
            $configValue !== Data::NO_VALUE_FOUND_CODE ? $configValue : null
        );
    }
}
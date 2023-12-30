<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Exceptions\AdapterException;
use CoRex\Config\Filesystem\FilesystemInterface;

/**
 * This adapter handles array files for project in folder specified.
 */
final class ProjectPathArrayFileAdapter extends ArrayFileAdapter
{
    public function __construct(FilesystemInterface $filesystem, string $projectRelativePathForPhpArrayFiles)
    {
        $pathToArrayFiles = $filesystem->getRootPath($projectRelativePathForPhpArrayFiles);

        if (!$filesystem->directoryExists($pathToArrayFiles)) {
            throw new AdapterException(
                sprintf(
                    'Path "%s" does not exist.',
                    $pathToArrayFiles
                )
            );
        }

        parent::__construct($filesystem, $pathToArrayFiles);
    }
}
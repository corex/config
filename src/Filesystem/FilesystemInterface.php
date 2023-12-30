<?php

declare(strict_types=1);

namespace CoRex\Config\Filesystem;

interface FilesystemInterface
{
    /**
     * Get root path.
     *
     * @param string|null $additional
     * @return string
     */
    public function getRootPath(?string $additional = null): string;

    /**
     * Check if directory exists.
     *
     * @param string $path
     * @return bool
     */
    public function directoryExists(string $path): bool;

    /**
     * Check if file exists.
     *
     * @param string $filename
     * @return bool
     */
    public function fileExists(string $filename): bool;

    /**
     * Require array from file.
     *
     * @param string $filename
     * @return array<int|string, mixed>
     */
    public function requireFileArray(string $filename): array;
}
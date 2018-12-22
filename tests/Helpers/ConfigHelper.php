<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Helpers;

use CoRex\Filesystem\Directory;
use CoRex\Filesystem\File;

/**
 * Class ConfigHelper
 *
 * Warning: This test will create needed temp-directories/files in
 * sys sys_get_temp_dir() every time you run it.
 */
class ConfigHelper
{
    /**
     * Prepare config files.
     *
     * @param string $path
     * @param string $identifier
     * @param mixed[] $data
     * @return string
     */
    public static function prepareConfigFiles(string $path, string $identifier, array $data): string
    {
        $filename = $path . '/' . $identifier . '.php';
        $varExport = '<' . "?php\nreturn " . var_export($data, true) . ";\n";
        if (Directory::isWritable($path)) {
            File::put($filename, $varExport);
        }
        return $path;
    }

    /**
     * Get unique path.
     *
     * @param string $suffix
     * @return string
     */
    public static function getUniquePath(string $suffix = ''): string
    {
        $tempDirectory = Directory::temp();
        $tempDirectory .= '/' . str_replace('.', '', microtime(true));
        if ($suffix !== '') {
            $tempDirectory .= '-' . $suffix;
        }
        Directory::make($tempDirectory);
        return $tempDirectory;
    }
}

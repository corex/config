<?php

namespace Tests\CoRex\Config\Helpers;

use CoRex\Support\System\Directory;
use CoRex\Support\System\File;

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
     * @param array $data
     * @return string
     */
    public static function prepareConfigFiles($path, $identifier, array $data)
    {
        $filename = $path . '/' . $identifier . '.php';
        $varExport = "<" . "?php\nreturn " . var_export($data, true) . ";\n";
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
    public static function getUniquePath($suffix = '')
    {
        $tempDirectory = Directory::temp();
        $tempDirectory .= '/' . str_replace('.', '', microtime(true));
        if ($suffix != '') {
            $tempDirectory .= '-' . $suffix;
        }
        Directory::make($tempDirectory);
        return $tempDirectory;
    }
}

<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Config\Adapter\EnvAdapter;
use CoRex\Config\Adapter\ProjectConfigArrayFileAdapter;
use CoRex\Config\Adapter\ProjectPathArrayFileAdapter;
use CoRex\Config\Adapter\ServerAdapter;
use CoRex\Config\Filesystem\Filesystem;
use CoRex\Config\Filesystem\FilesystemInterface;

class ConfigFactory implements ConfigFactoryInterface
{
    private FilesystemInterface $filesystem;

    public function __construct(?FilesystemInterface $filesystem = null)
    {
        $this->filesystem = $filesystem ?? new Filesystem();
    }

    /**
     * Create config-manager with project "config" adapter.
     *
     * Load order:
     * - Load from "config" subdirectory in root of project.
     *
     * @return ConfigInterface
     */
    public function createWithProjectConfigArrayFileAdapter(): ConfigInterface
    {
        return new Config([
            new ProjectConfigArrayFileAdapter($this->filesystem),
        ]);
    }

    /**
     * Create config-manager with project $path adapter.
     *
     * Load order:
     * - Load from specified subdirectory in root of project.
     *
     * @param string $projectRelativePath
     * @return ConfigInterface
     */
    public function createWithProjectPathArrayFileAdapter(string $projectRelativePath): ConfigInterface
    {
        return new Config([
            new ProjectPathArrayFileAdapter($this->filesystem, $projectRelativePath),
        ]);
    }

    /**
     * Create config manager with server, env and project "config" adapter.
     *
     * Load order:
     * - Load from $_SERVER.
     * - Load from $_ENV.
     * - Load from "config" subdirectory in root of project.
     *
     * @return ConfigInterface
     */
    public function createWithServerAndEnvAndProjectConfigArrayFileAdapter(): ConfigInterface
    {
        return new Config([
            new ServerAdapter(),
            new EnvAdapter(),
            new ProjectConfigArrayFileAdapter($this->filesystem),
        ]);
    }

    /**
     * Create config manager with server, env and project config adapter.
     *
     * Load order:
     * - Load from $_SERVER.
     * - Load from $_ENV.
     * - Load from specified subdirectory in root of project.
     *
     * @param string $projectRelativePath
     * @return ConfigInterface
     */
    public function createWithServerAndEnvAndProjectPathArrayFileAdapter(string $projectRelativePath): ConfigInterface
    {
        return new Config([
            new ServerAdapter(),
            new EnvAdapter(),
            new ProjectPathArrayFileAdapter($this->filesystem, $projectRelativePath),
        ]);
    }

    /**
     * Create config with ServerAdapter and EnvAdapter.
     *
     * Load order:
     * - Load from $_SERVER.
     * - Load from $_ENV.
     *
     * @return ConfigInterface
     */
    public function createWithServerAndEnvAdapter(): ConfigInterface
    {
        return new Config([
            new ServerAdapter(),
            new EnvAdapter(),
        ]);
    }
}
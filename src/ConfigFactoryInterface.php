<?php

declare(strict_types=1);

namespace CoRex\Config;

interface ConfigFactoryInterface
{
    /**
     * Create config with ProjectConfigArrayFileAdapter.
     *
     * Load order:
     * - Load from "config" subdirectory in root of project.
     *
     * @return ConfigInterface
     */
    public function createWithProjectConfigArrayFileAdapter(): ConfigInterface;

    /**
     * Create config with ProjectPathArrayFileAdapter.
     *
     * Load order:
     * - Load from specified subdirectory in root of project.
     *
     * @param string $projectRelativePath
     * @return ConfigInterface
     */
    public function createWithProjectPathArrayFileAdapter(string $projectRelativePath): ConfigInterface;

    /**
     * Create config with ServerAdapter, EnvAdapter and ProjectConfigArrayFileAdapter.
     *
     * Load order:
     * - Load from $_SERVER.
     * - Load from $_ENV.
     * - Load from "config" subdirectory in root of project.
     *
     * @return ConfigInterface
     */
    public function createWithServerAndEnvAndProjectConfigArrayFileAdapter(): ConfigInterface;

    /**
     * Create config with ServerAdapter, EnvAdapter and ProjectPathArrayFileAdapter.
     *
     * Load order:
     * - Load from $_SERVER.
     * - Load from $_ENV.
     * - Load from specified subdirectory in root of project.
     *
     * @param string $projectRelativePath
     * @return ConfigInterface
     */
    public function createWithServerAndEnvAndProjectPathArrayFileAdapter(string $projectRelativePath): ConfigInterface;

    /**
     * Create config manager with server and env adapters.
     *
     * Load order:
     * - Load from $_SERVER.
     * - Load from $_ENV.
     *
     * @return ConfigInterface
     */
    public function createWithServerAndEnvAdapter(): ConfigInterface;
}
<?php

declare(strict_types=1);

namespace CoRex\Config\Key;

interface KeyInterface
{
    /**
     * Get section of the key (first part of key).
     *
     * @return string
     */
    public function getSection(): string;

    /**
     * Get key parts.
     *
     * @param bool $excludeSection
     * @return array<string>
     */
    public function getParts(bool $excludeSection): array;

    /**
     * Get custom key.
     *
     * @param string $separator
     * @param bool $excludeSection
     * @return string
     */
    public function getCustom(string $separator, bool $excludeSection): string;

    /**
     * Get key in dot annotation format "my.config.var".
     *
     * @return string
     */
    public function getDotNotation(): string;

    /**
     * Get key in shout case format e.g. "MY_CONFIG_VAR".
     *
     * Also known as constant, macro, and scream case.
     *
     * @return string
     */
    public function getShoutCase(): string;

    /**
     * Get key in snake case "my_config_var".
     *
     * Also known as c case.
     *
     * @return string
     */
    public function getSnakeCase(): string;

    /**
     * Get key in kebab case e.g. "my-config-var".
     *
     * Also known as dash, hyphen, lisp, spinal and css case.
     *
     * @return string
     */
    public function getKebabCase(): string;
}
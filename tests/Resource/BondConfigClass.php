<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Resource;

use CoRex\Config\ConfigClassInterface;

class BondConfigClass implements ConfigClassInterface
{
    private string $firstname;
    private string $lastname;

    /**
     * @param array<string, string> $data
     */
    public function __construct(array $data)
    {
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @inheritDoc
     */
    public static function getSection(): string
    {
        return 'actor1';
    }
}
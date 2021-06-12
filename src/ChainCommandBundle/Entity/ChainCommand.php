<?php

namespace App\ChainCommandBundle\Entity;

class ChainCommand
{
    private bool $isMaster;
    private array $members;
    private string $parent;
    private string $name;

    public function __construct(string $name, bool $isMaster)
    {
        $this->name = $name;
        $this->isMaster = $isMaster;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isMaster(): bool
    {
        return $this->isMaster;
    }

    /**
     * @return array
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return $this->parent;
    }

    /**
     * @param array $members
     */
    public function setMembers(array $members): void
    {
        $this->members = $members;
    }

    /**
     * @param string $parent
     */
    public function setParent(string $parent): void
    {
        $this->parent = $parent;
    }
}

<?php

namespace App\ChainCommandBundle\Repository;

use App\ChainCommandBundle\Entity\ChainCommand;
use App\ChainCommandBundle\Storage\FileJsonStorage;

class ChainCommandRepository
{
    private FileJsonStorage $fileStorage;

    public function __construct()
    {
        $this->fileStorage = new FileJsonStorage('chain-commands');
    }

    public function addMasterCommand(string $masterCommand)
    {
        $chainsCommands = $this->fileStorage->getData();
        if ($this->isChainsHasCommand($chainsCommands, $masterCommand)) {
            throw new \Exception('Command already used');
        }
        $chainCommands[$masterCommand] = [];
        $this->fileStorage->setData($chainCommands);
    }

    public function addMemberCommand(string $masterCommand, string $memberCommand)
    {
        $chainCommands = $this->fileStorage->getData();
        if (!isset($chainCommands[$masterCommand])) {
            throw new \Exception('Master command not found');
        }
        if (isset($chainCommands[$memberCommand])) {
            throw new \Exception($memberCommand.' command used as master');
        }
        if (in_array($memberCommand, $chainCommands[$masterCommand])) {
            throw new \Exception('Member command already added');
        }
        $chainCommands[$masterCommand][] = $memberCommand;
        $this->fileStorage->setData($chainCommands);
    }

    public function getChainCommand($command): ?ChainCommand
    {
        $chainsCommands = $this->fileStorage->getData();
        if ($this->isChainsHasCommand($chainsCommands, $command)) {
            return $this->createChainCommand($chainsCommands, $command);
        }
        return null;
    }

    private function createChainCommand($chainsCommands, $command): ChainCommand
    {
        foreach ($chainsCommands as $master => $members) {
            if ($master === $command) {
                $chainCommand = new ChainCommand($command, true);
                $chainCommand->setMembers($members);
                return $chainCommand;
            } else {
                foreach ($members as $member) {
                    if ($member === $command) {
                        $chainCommand = new ChainCommand($command, false);
                        $chainCommand->setParent($master);
                        return $chainCommand;
                    }
                }
            }
        }
    }

    private function isChainsHasCommand(array $chainsCommands, string $command): bool
    {
        foreach ($chainsCommands as $master => $members) {
            if ($master === $command) {
                return true;
            }
            foreach ($members as $member) {
                if ($member === $command) {
                    return true;
                }
            }
        }
        return false;
    }
}

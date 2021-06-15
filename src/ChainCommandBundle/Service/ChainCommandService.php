<?php

namespace App\ChainCommandBundle\Service;

use App\ChainCommandBundle\Entity\ChainCommand;
use App\ChainCommandBundle\Exception\ChainCommandException;
use App\ChainCommandBundle\DataProvider\DataProviderInterface;
use App\ChainCommandBundle\DataProvider\JsonFileProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

class ChainCommandService
{
    private DataProviderInterface $fileStorage;
    private ContainerInterface $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
        $this->fileStorage = new JsonFileProvider($_ENV['CHAIN_COMMAND_FILE']);
    }

    /**
     * @throws ChainCommandException
     */
    public function addMasterCommand(string $masterCommand): void
    {
        $this->validateCommandExistsInApp($masterCommand);
        $chainCommands = $this->fileStorage->getData();
        $this->validateChainCommandNotExists($chainCommands, $masterCommand);
        $chainCommands[$masterCommand] = [];
        $this->fileStorage->setData($chainCommands);
    }

    /**
     * @throws ChainCommandException
     */
    public function addMemberCommand(string $masterCommand, string $memberCommand)
    {
        $this->validateCommandExistsInApp($masterCommand);
        $this->validateCommandExistsInApp($memberCommand);

        $chainCommands = $this->fileStorage->getData();
        if (!isset($chainCommands[$masterCommand])) {
            throw new ChainCommandException('Master command not found');
        }
        if (isset($chainCommands[$memberCommand])) {
            throw new ChainCommandException(sprintf('%s command used as master', $memberCommand));
        }
        if (in_array($memberCommand, $chainCommands[$masterCommand])) {
            throw new ChainCommandException('Member command already added');
        }
        $chainCommands[$masterCommand][] = $memberCommand;
        $this->fileStorage->setData($chainCommands);
    }

    public function getChainCommand($command): ?ChainCommand
    {
        $chainCommands = $this->fileStorage->getData();
        return $this->createChainCommand($chainCommands, $command);
    }

    private function createChainCommand($chainCommands, $command): ?ChainCommand
    {
        foreach ($chainCommands as $master => $members) {
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
        return null;
    }

    private function validateCommandExistsInApp($commandName)
    {
        if ($this->getCommandLoader()->has($commandName)) {
            throw new ChainCommandException(sprintf('Command %s is not registered in application', $commandName));
        }
    }

    private function validateChainCommandNotExists(array $chainCommands, string $command): void
    {
        foreach ($chainCommands as $master => $members) {
            if ($master === $command) {
                throw new ChainCommandException('Command already used');
            }
            foreach ($members as $member) {
                if ($member === $command) {
                    throw new ChainCommandException('Command already used');
                }
            }
        }
    }

    private function getCommandLoader(): CommandLoaderInterface{
        return $this->locator->get('console.command_loader');
    }
}

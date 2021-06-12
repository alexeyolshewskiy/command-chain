<?php

namespace App\ChainCommandBundle\Dispatcher;

use App\ChainCommandBundle\Entity\ChainCommand;
use App\ChainCommandBundle\Repository\ChainCommandRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class ChainCommandDispatcher
{
    private ChainCommandRepository $chainCommandRepository;
    private LoggerInterface $logger;

    public function __construct(ChainCommandRepository $chainCommandRepository, LoggerInterface $logger)
    {
        $this->chainCommandRepository = $chainCommandRepository;
        $this->logger = $logger;
    }

    public function dispatch(ConsoleCommandEvent $consoleCommandEvent)
    {
        $name = $consoleCommandEvent->getCommand()->getName();
        $chainCommand = $this->chainCommandRepository->getChainCommand($name);
        if ($chainCommand) {
            if ($chainCommand->isMaster()) {
                $this->processMasterCommand($chainCommand, $consoleCommandEvent);
            } else {
                $this->processMemberCommand($chainCommand, $consoleCommandEvent);
            }
        }
    }

    private function processMasterCommand(ChainCommand $chainCommand, ConsoleCommandEvent $consoleCommandEvent)
    {
        $command = $consoleCommandEvent->getCommand();

        $consoleCommandEvent->disableCommand();
        $this->logger->info(sprintf('Executing %s command itself first:', $chainCommand->getName()));
        $command->run($consoleCommandEvent->getInput(), $consoleCommandEvent->getOutput());

        $members = $chainCommand->getMembers();
        $this->logger->info(sprintf('Executing %s chain members:', $chainCommand->getName()));
        foreach ($members as $member) {
            $newCommand = $command->getApplication()->find($member);
            $newCommand->run($consoleCommandEvent->getInput(), $consoleCommandEvent->getOutput());
        }

        $this->logger->info(sprintf('Execution of %s chain completed.', $chainCommand->getName()));
    }

    private function processMemberCommand(ChainCommand $chainCommand, ConsoleCommandEvent $consoleCommandEvent)
    {
        $consoleCommandEvent->disableCommand();
        $message = sprintf('Error: %s command is a member of %s command chain and cannot be executed on its own.', $chainCommand->getName(), $chainCommand->getParent());
        $this->logger->info($message);
        $consoleCommandEvent->getOutput()->writeln($message);
    }
}

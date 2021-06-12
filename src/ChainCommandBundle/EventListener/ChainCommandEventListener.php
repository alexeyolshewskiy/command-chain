<?php

namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Dispatcher\ChainCommandDispatcher;
use App\ChainCommandBundle\Repository\ChainCommandRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class ChainCommandEventListener
{
    private ChainCommandDispatcher $chainCommandDispatcher;

    public function __construct(ChainCommandDispatcher $chainCommandDispatcher)
    {
        $this->chainCommandDispatcher = $chainCommandDispatcher;
    }

    public function onConsoleCommand(ConsoleCommandEvent $consoleCommandEvent)
    {
        $this->chainCommandDispatcher->dispatch($consoleCommandEvent);
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $consoleTerminateEvent)
    {
        $event = $consoleTerminateEvent;
    }

//    private function isCommandFromChains($command): bool{
//        $this->ChainCommandRepository->isChainCommand($command);
//    }
}

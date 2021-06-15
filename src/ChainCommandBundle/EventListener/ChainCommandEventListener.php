<?php

namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Dispatcher\ChainCommandDispatcher;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

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
}

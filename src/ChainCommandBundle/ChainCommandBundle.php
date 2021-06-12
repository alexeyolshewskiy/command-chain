<?php


namespace App\ChainCommandBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ChainCommandBundle extends Bundle
{
    public function __construct(EventDispatcher $eventDispatcher){
        $eventDispatcher->addListener(ConsoleEvents::COMMAND, [$this, 'chain']);
    }

private function chain(ConsoleCommandEvent $event) {
    // gets the input instance
$input = $event->getInput();

    // gets the output instance
$output = $event->getOutput();

    // gets the command to be executed
$command = $event->getCommand();

    // writes something about the command
$output->writeln(sprintf('Before running command <info>%s</info>', $command->getName()));

    // gets the application
$application = $command->getApplication();
}
}
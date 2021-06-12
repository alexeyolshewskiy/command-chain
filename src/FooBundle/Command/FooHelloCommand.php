<?php

namespace App\FooBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FooHelloCommand extends Command
{
    protected static $defaultName = 'foo:hello';
    protected static $defaultDescription = 'Add a short description for your command';

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello from Foo!');
    }
}

<?php

namespace App\BarBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BarHiCommand extends Command
{
    protected static $defaultName = 'bar:hi';
    protected static $defaultDescription = 'Add a short description for your command';

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hi from Bar!');
    }
}

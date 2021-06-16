<?php

namespace App\ChainCommandBundle\Command;

use App\ChainCommandBundle\Service\ChainCommandService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterMasterCommand extends Command
{
    protected static $defaultName = 'command-chain:register-master';
    protected static $defaultDescription = 'Register master chain command';
    private ChainCommandService $chainCommandService;
    private LoggerInterface $logger;

    public function __construct(ChainCommandService $chainCommandService, LoggerInterface $logger)
    {
        $this->chainCommandService = $chainCommandService;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('master', InputArgument::REQUIRED, 'Master command name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $master = $input->getArgument('master');
        $this->chainCommandService->addMasterCommand($master);
        $message = sprintf('%s is a master command of a command chain that has registered member commands', $master);
        $this->logger->info($message);
        $output->writeln($message);
    }
}

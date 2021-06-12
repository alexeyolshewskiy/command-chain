<?php

namespace App\ChainCommandBundle\Command;

use App\ChainCommandBundle\Repository\ChainCommandRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RegisterMasterCommand extends Command
{
    protected static $defaultName = 'command-chain:register-master';
    protected static $defaultDescription = 'Register master chain command';
    private ChainCommandRepository $ChainCommandRepository;
    private LoggerInterface $logger;

    public function __construct(ChainCommandRepository $ChainCommandRepository, LoggerInterface $logger)
    {
        $this->ChainCommandRepository = $ChainCommandRepository;
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
        $this->ChainCommandRepository->addMasterCommand($master);
        $this->logger->info(sprintf('%s is a master command of a command chain that has registered member commands', $master));
    }
}

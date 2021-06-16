<?php

namespace App\ChainCommandBundle\Command;

use App\ChainCommandBundle\Service\ChainCommandService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterMemberCommand extends Command
{
    protected static $defaultName = 'command-chain:register-member';
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
            ->addArgument('master', InputArgument::REQUIRED, 'Master command name')
            ->addArgument('member', InputArgument::REQUIRED, 'Member command name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $master = $input->getArgument('master');
        $member = $input->getArgument('member');
        $this->chainCommandService->addMemberCommand($master, $member);
        $message = sprintf('%s registered as a member of %s command chain', $member, $master);
        $this->logger->info($message);
        $output->writeln($message);
    }
}

<?php

namespace App\ChainCommandBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

class ChainCommandPositiveTest extends KernelTestCase
{
    use RemoveTrait;
    private static Application $application;

    private static string $masterCommandName = 'first:hi';
    private static string $masterCommandMessage = 'Hi from First!';
    private static string $memberCommandName = 'second:hi';
    private static string $memberCommandMessage = 'Hello from Second!';

    public static function setUpBeforeClass(): void
    {
        $kernel = static::createKernel();
        self::$application = new Application($kernel);
        self::$application->setAutoExit(false);
        $barHi = new Command(self::$masterCommandName);
        $barHi->setCode(function (InputInterface $input, OutputInterface $output) {
            $output->writeln(self::$masterCommandMessage);
        });
        self::$application->add($barHi);
        $fooHello = new Command(self::$memberCommandName);
        $fooHello->setCode(function (InputInterface $input, OutputInterface $output) {
            $output->writeln(self::$memberCommandMessage);
        });
        self::$application->add($fooHello);
    }

    public function testRegisterMasterCommand()
    {
        $applicationTester = new ApplicationTester(self::$application);
        $applicationTester->run([
            'command' => 'command-chain:register-master',
            'master' => self::$masterCommandName
        ]);

        $output = $applicationTester->getDisplay();
        $successMessage = sprintf('%s is a master command of a command chain that has registered member commands%s', self::$masterCommandName, PHP_EOL);
        $this->assertEquals($successMessage, $output);
    }

    public function testRegisterMemberCommand()
    {
        $applicationTester = new ApplicationTester(self::$application);
        $applicationTester->run([
            'command' => 'command-chain:register-member',
            'master' => self::$masterCommandName,
            'member' => self::$memberCommandName,
        ]);
        $output = $applicationTester->getDisplay();
        $successMessage = sprintf('%s registered as a member of %s command chain%s', self::$memberCommandName, self::$masterCommandName, PHP_EOL);
        $this->assertEquals($successMessage, $output);
    }

    public function testRunMasterCommand()
    {
        $applicationTester = new ApplicationTester(self::$application);
        $applicationTester->run([
            'command' => self::$masterCommandName,
        ]);

        $output = $applicationTester->getDisplay();
        $successMessage = sprintf('%s%s%s%s', self::$masterCommandMessage, PHP_EOL, self::$memberCommandMessage, PHP_EOL);
        $this->assertEquals($successMessage, $output);
    }

    public function testRunMemberCommand()
    {
        $applicationTester = new ApplicationTester(self::$application);
        $applicationTester->run([
            'command' => self::$memberCommandName,
        ]);

        $output = $applicationTester->getDisplay();
        $successMessage = sprintf('Error: %s command is a member of %s command chain and cannot be executed on its own.%s', self::$memberCommandName, self::$masterCommandName, PHP_EOL);
        $this->assertEquals($successMessage, $output);
    }

    public static function tearDownAfterClass(): void
    {
        self::removeChainCommandTestFile();
    }
}

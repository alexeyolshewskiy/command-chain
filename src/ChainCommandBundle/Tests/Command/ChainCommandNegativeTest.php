<?php

namespace App\ChainCommandBundle\Tests\Command;

use App\ChainCommandBundle\Exception\ChainCommandException;
use App\ChainCommandBundle\Service\ChainCommandService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChainCommandNegativeTest extends WebTestCase
{
    use RemoveTrait;

    private static string $masterCommandName = 'first:hi';
    private static string $memberCommandName = 'second:hi';

    public function setUp(): void
    {
        self::bootKernel();
        self::removeChainCommandTestFile();
    }

    public function testTwiceRegisterMaster()
    {
        $chainService = $this->getChainService();
        $chainService->addMasterCommand(self::$masterCommandName);
        $this->expectException(ChainCommandException::class);
        $this->expectExceptionMessage('Command already used');
        $chainService->addMasterCommand(self::$masterCommandName);
    }

    public function testMemberAsMasterRegister()
    {
        $chainService = $this->getChainService();
        $chainService->addMasterCommand(self::$masterCommandName);
        $chainService->addMemberCommand(self::$masterCommandName, self::$memberCommandName);
        $this->expectException(ChainCommandException::class);
        $this->expectExceptionMessage('Command already used');
        $chainService->addMasterCommand(self::$memberCommandName);
    }

    public function testMasterAsMemberRegister()
    {
        $chainService = $this->getChainService();
        $chainService->addMasterCommand(self::$masterCommandName);
        $chainService->addMasterCommand('another:master');
        $this->expectException(ChainCommandException::class);
        $this->expectExceptionMessage(sprintf('%s command used as master', self::$masterCommandName));
        $chainService->addMemberCommand('another:master', self::$masterCommandName);
    }

    public function testRegisterMemberWithNotFoundMasterRegister()
    {
        $chainService = $this->getChainService();
        $this->expectException(ChainCommandException::class);
        $this->expectExceptionMessage('Master command not found');
        $chainService->addMemberCommand('not:found', self::$memberCommandName);
    }

    public function testMemberAlreadyAdded()
    {
        $chainService = $this->getChainService();
        $chainService->addMasterCommand(self::$masterCommandName);
        $chainService->addMemberCommand(self::$masterCommandName, self::$memberCommandName);
        $this->expectException(ChainCommandException::class);
        $this->expectExceptionMessage('Member command already added');
        $chainService->addMemberCommand(self::$masterCommandName, self::$memberCommandName);
    }

    public static function tearDownAfterClass(): void
    {
        self::removeChainCommandTestFile();
    }

    private function getChainService(): ChainCommandService
    {
        return self::$container->get('chain.command.service');
    }
}

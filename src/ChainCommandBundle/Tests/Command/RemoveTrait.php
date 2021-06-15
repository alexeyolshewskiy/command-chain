<?php

namespace App\ChainCommandBundle\Tests\Command;

trait RemoveTrait
{
    private static function removeChainCommandTestFile()
    {
        $file = $_ENV['CHAIN_COMMAND_FILE'];
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

<?php
namespace Wbaiyy\Tests;

use Composer\IO\ConsoleIO;

class IO extends ConsoleIO
{
    public function __construct()
    {
    }

    public function write($messages, $newline = true, $verbosity = self::NORMAL)
    {
        echo $messages;
    }
}

class Filesystem extends \Composer\Util\Filesystem
{
    public function remove($file)
    {
        return false !== strpos($file, 'return-true');
    }
}

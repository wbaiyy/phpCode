<?php
namespace ego\tests\log;

use ego\log\FileTarget;
use ego\tests\TestCase;
use yii\log\Logger;

class FileTargetTest extends TestCase
{
    public function testFormatMessage()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $target = new FileTarget();
        $message = $target->formatMessage([
            'message',
            Logger::LEVEL_INFO,
            'category',
            time(),
        ]);
        $this->assertTrue(false !== strpos($message, 'category'));

        $message = $target->formatMessage([
            ['test' => 'test'],
            Logger::LEVEL_INFO,
            'category',
            time(),
        ]);
        $this->assertTrue(false !== strpos($message, "'test' => 'test'"), $message);

        $message = $target->formatMessage([
            new \Exception(__FILE__),
            Logger::LEVEL_INFO,
            'category',
            time(),
            [
                ['file' => __FILE__, 'line' => __LINE__]
            ]
        ]);
        $this->assertTrue(false !== strpos($message, __FILE__));

        $message = $target->formatMessage([
            'message',
            Logger::LEVEL_INFO,
            'category',
            time(),
            [
                ['file' => __FILE__, 'line' => __LINE__]
            ]
        ]);
        $this->assertTrue(false !== strpos($message, __FILE__));
    }
}

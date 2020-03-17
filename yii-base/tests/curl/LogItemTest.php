<?php
namespace ego\tests\curl;

use ego\curl\LogItem;
use ego\tests\TestCase;

class LogItemTest extends TestCase
{
    public function testGetMessage()
    {
        $logItem = new LogItem();
        $logItem->response = 'test';

        $this->assertEquals('test', $this->invokeMethod($logItem, 'getMessage')['response']);
        $logItem->maxResponseLength = 1;
        $this->assertNotEquals('test', $this->invokeMethod($logItem, 'getMessage')['response']);
    }

    public function testProcessParams()
    {
        $params = [
            'password' => '123456'
        ];
        $logItem = new LogItem([
            'params' => $params
        ]);

        $this->assertEquals(
            '123456',
            $this->invokeMethod($logItem, 'processParams', [$params])['password']
        );
        $logItem->sensitiveFields = ['password'];
        $this->assertEquals(
            '**********',
            $this->invokeMethod($logItem, 'processParams', [$params])['password']
        );
    }
}

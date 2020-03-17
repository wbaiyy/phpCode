<?php
namespace ego\tests\soa;

use ego\soa\LogItem;
use ego\tests\TestCase;

class LogItemTest extends TestCase
{
    public function testGetMessage()
    {
        $logItem = new LogItem();
        $logItem->baseUrl = '127.0.0.1:8090';
        $logItem->uri = 'getUserInfo';
        $logItem->method = 'com.Wbaiyy.spi.mgoods.girlbest.inter.IGoodsListAggregateService';
        $message = $this->invokeMethod($logItem, 'getMessage');
        $this->assertFalse(isset($message['base_url'], $message['uri']));
        $clone = clone $logItem;
        $this->assertEquals($clone->baseUrl, $message['ip']);
        $this->assertEquals($clone->uri, $message['method']);
        $this->assertEquals($clone->method, $message['service']);
    }
}

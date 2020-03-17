<?php
namespace ego\tests\mq;

use ego\tests\TestCase;
use Wbaiyy\RabbitMQ\Exceptions\ReceiveException;
use Wbaiyy\RabbitMQ\Exceptions\SendException;

class ClientTest extends TestCase
{
    public function testSend()
    {
        app()->mq->send('phpunit', null);
        $this->assertContains(
            'notfound',
            app()->mq->slient()->send('notfound', null)
        );

        $this->expectException(SendException::class);
        app()->mq->slient(false)->send('notfound', []);
    }

    public function testReceive()
    {
        $this->assertContains(
            'notfound',
            app()->mq->slient()->receive('notfound', function() {})
        );

        $this->expectException(ReceiveException::class);
        app()->mq->slient(false)->receive('notfound', function() {});
    }
}

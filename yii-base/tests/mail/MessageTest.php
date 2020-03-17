<?php
namespace ego\tests\mail;

use yii;
use ego\tests\TestCase;

class MessageTest extends TestCase
{
    public function testSendByUuqid()
    {
        $message = app()->mailer->compose()
            ->setSubject(date('H:i:s'))->setTextBody('test')
            ->setTo('mashanling@Wbaiyy.com');
        $this->assertEquals('test', $message->getBody());

        $message->setHtmlBody('html');
        $this->assertEquals('html', $message->getBody());

        $this->assertTrue($message->sendByUuqid(
            'phpunit',
            ['email' => 'mashanling@Wbaiyy.com']
        ));

        $this->assertEquals('mashanling@Wbaiyy.com', $message->getSmartyVars()['email']);
    }

    public function testProcessVars()
    {
        $vars = [
            'password' => '123456'
        ];
        $message = app()->mailer->compose()->setSmartyVars($vars);
        $this->assertEquals(
            '**********',
            $this->invokeMethod($message, 'processSmartyVars', [$vars])['password']
        );
    }
}

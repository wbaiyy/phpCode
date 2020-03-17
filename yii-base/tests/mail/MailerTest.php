<?php
namespace ego\tests\mail;

use ego\mail\Mailer;
use ego\mail\Message;
use yii;
use ego\tests\TestCase;

class MailerTest extends TestCase
{
    public function testSend()
    {
        $this->assertInstanceOf(Mailer::class, app()->mailer);

        $message = app()->mailer->compose()
            ->setSubject(date('H:i:s'))->setTextBody('test')
            ->setTo('mashanling@Wbaiyy.com');
        $this->assertInstanceOf(Message::class, $message);
        $this->assertTrue(app()->mailer->send($message));

        app()->mailer->setTransport(['class' => \Swift_SmtpTransport::class, 'port' => 3600]);
        app()->mailer->useFileTransport = false;
        $this->assertFalse(is_bool(app()->mailer->send($message)));
        $this->assertNotNull(app()->mailer->getError());

        app()->mailer->only = ['example.com'];
        $this->assertTrue(false !== strpos(
            app()->mailer->send($message),
            '发送email不在指定范围内'
        ));
    }

    public function testSendByUuqid()
    {
        $username = 'mashanling';
        $email = 'mashanling@Wbaiyy.com';
        $message = app()->mailer->compose()
            ->setSubject(date('H:i:s'))->setTextBody('test')
            ->setTo('mashanling@Wbaiyy.com');
        $this->assertTrue(app()->mailer->sendByUuqid(
            $message,
            'phpunit',
            ['email' => $email]
        ));
        $this->assertEquals('{$username}', $message->getSubject());

        app()->mailer->sendByUuqid(
            $message,
            'phpunit',
            [
                'email' => $email,
                'subject_params' => ['username' => $username]
            ]
        );
        $this->assertEquals($username, $message->getSubject());
        $this->assertEquals('phpunit', app()->mailer->getUuqid());
        $this->assertNotNull($this->invokeProperty(app()->mailer, 'sendStartTime'));
        $this->assertNotNull($this->invokeProperty(app()->mailer, 'sendEndTime'));
        $this->assertTrue(is_numeric(app()->mailer->getSendUsedTime()));
    }

    public function testInit()
    {
        app()->mailer->only = ['example.com', 'xxx' => null];
        app()->mailer->init();
        $this->assertCount(1, app()->mailer->only);
    }
}

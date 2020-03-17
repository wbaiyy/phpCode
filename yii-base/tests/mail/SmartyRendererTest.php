<?php
namespace ego\tests\mail;

use yii;
use ego\tests\TestCase;

class SmartyRendererTest extends TestCase
{
    public function testFetch()
    {
        try {
            app()->mailer->getRenderer()->smarty->fetch('notfound');
        } catch (yii\base\InvalidValueException $e) {
        }
        $this->assertTrue(isset($e));


        $email = 'foo@example.com';
        app()->mailer->getRenderer()->smarty->assign('email', $email);
        $this->assertEquals(
            $email,
            app()->mailer->getRenderer()->smarty->fetch('phpunit')
        );

        app()->mailer->getRenderer()->smarty->assign('email', 'foo');
        $this->assertEquals(
            'foo',
            app()->mailer->getRenderer()->smarty->fetch('phpunit')
        );

        $this->expectException(yii\base\ErrorException::class);
        $this->expectExceptionMessage('Undefined index: email');
        app()->mailer->getRenderer()->smarty->clearAllAssign();
        app()->mailer->getRenderer()->smarty->fetch('phpunit');
    }
}

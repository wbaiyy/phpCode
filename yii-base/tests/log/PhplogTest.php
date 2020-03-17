<?php
namespace ego\tests\log;

use ego\tests\TestCase;
use yii\base\UserException;

class PhplogTest extends TestCase
{
    public function testGet()
    {
        app()->phplog->root = __DIR__ . '/20170330';
        $this->assertTrue(false !== strpos(
            app()->phplog->get('20/../app.txt'),
            '不能使用相对路径'
        ));

        app()->phplog->maxSize = 0.001;
        $this->assertTrue(false !== strpos(
            app()->phplog->get('app.log'),
            '文件大小超出限制'
        ));

        app()->phplog->maxSize = 1024;
        $this->assertTrue(false !== strpos(app()->phplog->get('app.log'), 'curl-soa'));

        $this->assertTrue(false !== strpos(
            app()->phplog->get('notfond.log'),
            '不存在'
        ));

        $this->assertArrayHasKey('app.log.1', app()->phplog->get(''));
        $this->assertArrayHasKey(
            '2017' . DIRECTORY_SEPARATOR .'app.log.1',
            app()->phplog->get('2017')
        );
    }

    public function testInit()
    {
        app()->phplog->endTime = time() - 86400;
        $this->expectException(UserException::class);
        app()->phplog->init();
    }
}

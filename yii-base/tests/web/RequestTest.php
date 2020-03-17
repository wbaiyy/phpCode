<?php
namespace ego\tests\web;

use ego\tests\TestCase;

class RequestTest extends TestCase
{
    public function testPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['name'] = 'POST';
        $_GET['name'] = 'GET';
        $this->assertSame('POST', app()->request->post('name'));

        app()->env->env = 'dev';
        $this->assertSame('POST', app()->request->post('name'));

        $_GET['DEBUG'] = true;
        $this->assertSame('GET', app()->request->post('name'));
    }

    public function testGetUserIp()
    {
        $this->assertSame(app()->ip->get(), app()->request->getUserIP());
    }

    public function testGetRequestId()
    {
        $this->assertTrue(32 == strlen(app()->request->getRequestId()));
    }

    public function testCreateCsrfCookie()
    {
        /** @var \yii\web\Cookie $cookie */
        app()->request->csrfCookieDomain = 'test';
        $cookie = $this->invokeMethod(app()->request, 'createCsrfCookie', ['test']);
        $this->assertEquals('test', $cookie->domain);
    }
}

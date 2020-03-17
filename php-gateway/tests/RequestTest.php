<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Gateway\Client;
use Wbaiyy\Gateway\Request;
use Wbaiyy\Gateway\Response;

class RequestTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        unset($_COOKIE);
    }

    public function testSetPreRelease_and_AddHeader()
    {
        $url = 'http://www.php-gateway-test.com.laihuan.dev65.egocdn.com/';
        $request = new Request($url);
        $httpResponse = $request->send([]);
        $this->assertnotContains('staging', $httpResponse);

        $_COOKIE['staging'] = 'true';
        $httpResponse = $request->send([]);
        $this->assertContains('staging', $httpResponse);
    }

    public function testIsPreRelease()
    {
        $request = new Request(PHPUNIT_GATEWAY_URL);
        $actual = $request->isPreRelease();
        $this->assertEquals(false, $actual);

        $_COOKIE['staging'] = 'true';
        $actual = $request->isPreRelease();
        $this->assertEquals(true, $actual);
    }

    public function testSetTimeOut()
    {
        $request = new Request(PHPUNIT_GATEWAY_URL);
        $request->setTimeOut();
    }
}

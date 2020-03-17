<?php
namespace ego\tests\curl;

use ego\curl\BaseResponseCurl;
use ego\tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class BaseResponseCurlTest extends TestCase
{
    /**
     * @var BaseResponseCurl
     */
    protected $curl;

    public function setUp()
    {
        parent::setUp();
        $this->curl = new BaseResponseCurl();
    }

    public function testRequest()
    {
        try {
            $this->curl->request('get', 'uri');
        } catch (GuzzleException $e) {
        }
        $this->assertTrue(isset($e));

        $this->assertNull($this->curl->slient()->request('get', 'uri'));

        // response
        $this->invokeProperty(
            $this->curl,
            'response',
            new Response(200, [], 'test')
        );
        /** @var Response $result */
        $result = $this->curl->slient(false)->request('get', 'uri', []);
        $this->assertSame('test', $result->getBody() . '');

        // ok
        $this->invokeProperty(
            $this->curl,
            'client',
            new Client([
                'base_uri' => app()->params['service']['url']
            ])
        );
        $result = $this->curl->slient(false)->request('get', '');
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testGetClient()
    {
        $client = $this->curl->getClient();
        $client2 = $this->curl->getClient();
        $client3 = $this->curl->getClient(true);
        $this->assertSame(spl_object_hash($client), spl_object_hash($client2));
        $this->assertNotSame(spl_object_hash($client), spl_object_hash($client3));
    }

    public function testEnableLog()
    {
        $this->assertSame(app()->env->isLocal(), $this->curl->enableLog);

        $this->curl->enableLog(false);
        $this->assertFalse($this->curl->enableLog);

        $this->curl->enableLog(true);
        $this->assertTrue($this->curl->enableLog);

        $this->curl->enableLog(123);
        $this->assertTrue(is_bool($this->curl->enableLog));

        $this->curl->enableLog(null);
        $this->assertTrue(is_bool($this->curl->enableLog));
    }

    public function testInit()
    {
        $this->assertArrayHasKey('connect_timeout', $this->curl->guzzleOptions);
    }

    public function testShouldThrowException()
    {
        $this->assertFalse(
            $this->invokeMethod($this->curl, 'shouldThrowException', [0, false])
        );
        $this->assertTrue(
            $this->invokeMethod($this->curl, 'shouldThrowException', [10000, false])
        );
        $this->assertFalse(
            $this->invokeMethod($this->curl, 'shouldThrowException', [10000, true])
        );
        $this->assertFalse(
            $this->invokeMethod($this->curl, 'shouldThrowException', [10000, [10000, 9999]])
        );
    }
}

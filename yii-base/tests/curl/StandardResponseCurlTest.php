<?php
namespace ego\tests\curl;

use ego\enums\CommonError;
use ego\curl\StandardResponseCurl;
use ego\curl\ResponseException;
use ego\tests\TestCase;
use GuzzleHttp\Psr7\Response;

class StandardResponseCurlTest extends TestCase
{
    /**
     * @var StandardResponseCurl
     */
    protected $curl;

    public function setUp()
    {
        parent::setUp();
        $this->curl = new StandardResponseCurl();
    }

    public function testRequest()
    {
        $result = $this->curl->slient()->request('method', 'uri');
        $this->assertSame(CommonError::ERR_CURL_REQUEST_FAIL, $result->code);
        $result = $this->curl->request('get', app()->params['service']['url']);
        $this->assertSame(0, $result->code);

        // asArray
        $this->assertFalse(is_array($result));
        $result = $this->curl->asArray()->request(
            'get',
            $this->getUrl('test/index/mock-send')
        );
        $this->assertTrue(is_array($result));
    }

    public function testParseResponse()
    {
        $result = $this->invokeMethod(
            $this->curl,
            'parseResponse',
            [new Response(200, [], json_encode(['code' => 0])), false]
        );
        $this->assertSame(0, $result['code']);
        /** @var ResponseException $e */
        try {
            $this->invokeMethod(
                $this->curl,
                'parseResponse',
                [new Response(200, [], json_encode([])), false]
            );
        } catch (ResponseException $e) {
        }
        $this->assertInstanceOf(Response::class, $e->response);

        unset($e);
        try {
            $this->invokeMethod(
                $this->curl,
                'parseResponse',
                [['code' => 10000], false]
            );
        } catch (ResponseException $e) {
        }
        $this->assertTrue(isset($e));

        $result = $this->invokeMethod(
            $this->curl->slient(),
            'parseResponse',
            [new Response(200, [], json_encode(['code' => 10000])), true]
        );
        $this->assertSame(10000, $result['code']);

        // 指定错误码不抛出异常
        $result = $this->invokeMethod(
            $this->curl->slient(),
            'parseResponse',
            [new Response(200, [], json_encode(['code' => 10000])), 10000]
        );
        $this->assertSame(0, $result['code']);
        $this->assertSame(10000, $result['rawCode']);

        $result = $this->invokeMethod(
            $this->curl->slient(),
            'parseResponse',
            [[], true]
        );
        $this->assertSame(CommonError::ERR_CURL_RESPONSE_FAIL, $result['code']);
    }

    protected function getUrl($route)
    {
        return app()->params['service']['url'] . '/' . $route;
    }
}

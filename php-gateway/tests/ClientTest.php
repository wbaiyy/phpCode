<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Gateway\Client;
use Wbaiyy\Gateway\Request;
use Wbaiyy\Gateway\Response;
use Wbaiyy\Gateway\Exceptions\RequestException;
use Wbaiyy\Gateway\Exceptions\ResponseException;

class ClientTest extends TestCase
{
    public function testSend()
    {
        $e = null;
        try {
            // 不存在的module
            $this->buildClient()->send('notfound', []);
        } catch (RequestException $e) {
        }
        $this->assertInstanceOf(RequestException::class, $e);

        try {
            // http code 500
            $this->buildClient(PHPUNIT_GATEWAY_UPSTREAM_URL)->send('', ['http_code' => 500]);
        } catch (RequestException $e) {
        }
        $this->assertSame(500, $e->getCode());

        try {
            $this->buildClient(PHPUNIT_GATEWAY_UPSTREAM_URL)->send('', ['code' => 100]);
        } catch (ResponseException $e) {
        }
        $this->assertInstanceOf(ResponseException::class, $e);
        $this->assertSame(100, $e->getCode());
        $this->assertArrayHasKey('sign', $e->getClient()->getRequestBody());

        $request = null;
        $response = null;
        $client = $this->buildClient(PHPUNIT_GATEWAY_UPSTREAM_URL)->onSent(
            function (Client $client) use (&$request, &$response) {
                $request = $client->getRequest();
                $response = $client->getResponse();
            }
        );
        $result = $client->send('', []);
        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(0, $result->code);
    }

    public function testBuildRequestBody()
    {
        $client = $this->buildClient();
        $body = $this->invokeMethod($client, 'buildRequestBody', ['method', [], null]);
        $this->assertSame('method', $body['method']);
        $this->assertSame('method', $body['module']);

        $body = $this->invokeMethod($client, 'buildRequestBody', ['method', [], 'module']);
        $this->assertSame('module', $body['module']);
    }

    /**
     * @param $url
     * @param $key
     * @param $serect
     * @return Client
     */
    private function buildClient($url = null, $key = null, $serect = null)
    {
        return new Client(
            $url ?: PHPUNIT_GATEWAY_URL,
            $key ?: PHPUNIT_GATEWAY_APP_KEY,
            $serect ?: PHPUNIT_GATEWAY_SECRET
        );
    }
}

<?php
namespace ego\tests\soa;

use ego\enums\CommonError;
use ego\soa\LogItem;
use ego\soa\Service;
use app\services\goods\GoodsService;
use app\services\user\UserService;
use ego\soa\SoaException;
use ego\tests\enums\UserError;
use ego\tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * @requires function testSoaService
 */
class ServiceTest extends TestCase
{
    /**
     * @var Service
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new Service();
    }

    public function testSend()
    {
        $this->invokeProperty(
            $this->service,
            'client',
            new Client([
                'base_uri' => $this->getUrl('test/index/mock-send')
            ])
        );
        $this->assertSame(
            0,
            $this->service->send('service.{sitename}', 'method')->code
        );

        /** @var LogItem $logItem */
        $logItem = $this->invokeProperty($this->service, 'logItem');
        $this->assertEquals('service.' . app()->site->name, $logItem->method);

        app()->site->code2names['test'] = 'test';
        $this->service->send('service.{sitename}', 'method', ['siteCode' => 'test']);
        $logItem = $this->invokeProperty($this->service, 'logItem');
        $this->assertEquals('service.test', $logItem->method);
    }

    public function testInit()
    {
        $this->assertEquals(
            app()->params['service']['url'],
            $this->invokeProperty($this->service, 'url')
        );
        $this->invokeProperty($this->service, 'url', 'url');
        $this->assertEquals(
            'url',
            $this->invokeProperty($this->service, 'url')
        );
    }

    public function testGetMessage()
    {
        $this->assertSame(
            CommonError::getMessage(CommonError::ERR_SYSTEM_BUSY),
            $this->service->getMessage(10000)
        );

        $this->invokeProperty($this->service, 'code2error.1', UserError::class);
        $this->invokeStaticProperty(
            UserError::class,
            'javaCode2phpCode.10000',
            UserError::ERR_PASSWORD_EMPTY
        );
        $this->assertSame(
            UserError::getMessageByJavaCode(10000),
            $this->service->getMessage(10000)
        );
    }

    public function testRequestInternal()
    {
        // ok
        $this->invokeProperty(
            $this->service,
            'client',
            new Client([
                'base_uri' => $this->getUrl('test/index/mock-send')
            ])
        );
        /** @var \GuzzleHttp\Psr7\Response $result */
        $result = $this->invokeMethod(
            $this->service,
            'requestInternal',
            ['service', 'method', []]
        );
        $this->assertSame(0, json_decode($result->getBody())->code);
    }

    public function testBuildRequestBody()
    {
        $body = $this->invokeMethod(
            $this->service,
            'buildRequestBody',
            ['service', 'method']
        );
        $this->assertEquals(Service::DOMAIN, $body['header']['domain']);

        $body = $this->invokeMethod(
            app()->Goods->GoodsService,
            'buildRequestBody',
            ['service', 'method']
        );
        $this->assertEquals(GoodsService::DOMAIN, $body['header']['domain']);

        $body = $this->invokeMethod(
            app()->User->UserService,
            'buildRequestBody',
            ['service', 'method']
        );
        $this->assertEquals(UserService::DOMAIN, $body['header']['domain']);
    }

    public function testParseResponse()
    {
        $result = $this->invokeMethod(
            $this->service,
            'parseResponse',
            [new Response(200, [], json_encode(['code' => 10000, 'message' => 'fail'])), true]
        );
        $this->assertSame(
            CommonError::getMessage(CommonError::ERR_SYSTEM_BUSY),
            $result['message']
        );

        $this->expectException(SoaException::class);
        $this->expectExceptionMessage(CommonError::getMessage(CommonError::ERR_SYSTEM_BUSY));
        $this->invokeMethod(
            $this->service,
            'parseResponse',
            [new Response(200, [], json_encode(['code' => 10000, 'message' => 'fail'])), false]
        );
    }

    protected function getUrl($route)
    {
        return app()->params['service']['url'] . '/' . $route;
    }
}

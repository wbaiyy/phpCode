<?php
namespace ego\tests\soa;

use ego\enums\CommonError;
use ego\soa\SocketService;
use app\services\goods\GoodsService;
use app\services\user\UserService;
use ego\tests\enums\UserError;
use ego\tests\TestCase;
use GuzzleHttp\Psr7\Response;
use ego\soa\socket\Client as SocketClient;
use ego\socket\exception\SocketException;
use yii\base\ErrorException;
use Google\Protobuf\Internal\InputStream;
/**
 * @requires function testSocketService
 */
class SocketServiceTest extends TestCase
{
    /**
     * @var Service
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new SocketService();
    }

    public function testInit()
    {
        $this->assertEquals(
            app()->params['service']['tcp_address'],
            $this->invokeProperty($this->service, 'address')
        );
        $this->invokeProperty($this->service, 'address', 'address');
        $this->assertEquals(
            'address',
            $this->invokeProperty($this->service, 'address')
        );
    }

    public function testGetClient()
    {
        $this->invokeProperty($this->service, 'address', '10.40.2.64:2087');

        $this->service->getClient(true);
        $this->assertInstanceOf(SocketClient::class, $this->service->getClient(true));
        $this->assertInstanceOf(SocketClient::class, $this->service->getClient());

        $this->invokeProperty($this->service, 'address', 'address');
        try {
            $this->service->getClient(true);
        } catch (SocketException $e) {
            return;
        }
        $this->fail('预期的异常未出现');
    }

    public function testBuildRequestBody()
    {
        $res = $this->invokeMethod($this->service, 'buildRequestBody', [
            'service',
            'method'
        ]);

        $this->assertNotNull($res);

        $res = $this->invokeMethod($this->service, 'buildRequestBody', [
            'service',
            'method',
            [
                'siteCode' => false
            ]
        ]);

        $this->assertNotNull($res);
    }

    public function testGetByte()
    {
        $res = $this->invokeMethod($this->service, 'getByte', [
            17
        ]);
        $res = unpack('c', $res);
        $this->assertEquals(17, $res[1]);

        $res = $this->invokeMethod($this->service, 'getByte', [
            171
        ]);

        $input = new InputStream($res);
        $input->readVarint32($res);

        $this->assertEquals(171, $res);
    }

    public function testParseSocketResponse()
    {
        $body = $this->invokeMethod($this->service, 'buildRequestBody', [
            'service',
            'method'
        ]);
        $response = $this->service->parseSocketResponse($this->service->getClient()
            ->post($body));
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(json_decode($response->getBody(), true)['code'], 11);
    }

    public function testRequestInternal()
    {
        $this->invokeProperty($this->service, 'address', '10.40.2.64:2087');
        $response = $this->invokeMethod($this->service, 'requestInternal', [
            'service',
            'method',
            []
        ]);
        $this->assertInstanceOf(Response::class, $response);
        $res = json_decode($response->getBody(), true);
        $this->assertNotEquals($res['code'], 0);
    }
}

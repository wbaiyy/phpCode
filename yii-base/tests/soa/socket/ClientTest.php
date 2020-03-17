<?php
namespace ego\tests\soa\socket;

use ego\enums\CommonError;
use ego\soa\Service;
use app\services\goods\GoodsService;
use app\services\user\UserService;
use ego\soa\SoaException;
use ego\tests\enums\UserError;
use ego\tests\TestCase;
use ego\socket\exception\SocketException;
use ego\soa\socket\Client;
use ego\soa\SocketService;

/**
 * @requires function testSoaSocketClient
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->client = new Client(['address' => '10.40.2.64:2087']);
    }

    public function testPost()
    {
		$obj = new SocketService();

		$res = $this->invokeMethod(
			$obj,
			'buildRequestBody',
			[
				'serivice',
				'method',
				[
				]
			]
		);
		$this->assertNotNull($this->client->post($res));


		$str = 'S
(
com.IHelloServersay2goods"1.1.1(' . "'" . '{"name":"IHelloServer","pwd":"1234567"}';

		try {
			$this->client->post($str);
		} catch (SocketException $e) {
			return;
		}
		$this->fail('except a SocketException');

    }
}

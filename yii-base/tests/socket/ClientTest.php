<?php
namespace ego\tests\socket;

use ego\enums\CommonError;
use ego\soa\Service;
use app\services\goods\GoodsService;
use app\services\user\UserService;
use ego\soa\SoaException;
use ego\socket\exception\SocketException;
use ego\tests\enums\UserError;
use ego\tests\TestCase;
use ego\socket\Client;
use GuzzleHttp\Psr7\Response;

/**
 * @requires function testSocketClient
 */
class ClientTest extends TestCase
{
    /**
     * @var client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->client = new Client(['address' => '10.40.2.64:2087']);
    }

    public function testConnect()
    {
		$this->invokeProperty(
            $this->client,
            'address',
			'10.40.2.64:2087'
        );
		$this->client->connect();
        $this->assertNotNull(
           $this->invokeProperty($this->client, 'fp')
        );
		
		$this->invokeProperty(
            $this->client,
            'address',
			'127.0.0.1:2087'
        );
		try {
			$this->client->connect();
		} catch (SocketException $e) {
			return;
		}
		$this->fail("need catch SocketException");
    }
	

    public function testInit()
    {
         $this->assertNotNull(
           $this->invokeProperty($this->client, 'fp')
        );
    }

    public function testWrite()
    {	
		$this->assertTrue($this->client->write('123456'));
		
    }

    public function testRead()
    {	
		$str = '_
(
com.IHelloServersay2goods"1.1.1(3{"name":"IHelloServerIHelloServer","pwd":"1234567"}';
		$this->client->write($str);
		
		$this->assertNotNull($this->client->read(10));
		
		
    }
	
	public function testSetAddress()
	{
		$this->client->setAddress('123456');
		$this->assertEquals($this->client->getAddress(), '123456');
	}
	
	public function testGetAddress()
	{
		$this->assertEquals($this->client->getAddress(), '10.40.2.64:2087');
	}
	
	public function test__destruct()
	{
		$this->client->__destruct();
		$this->assertEquals(get_resource_type($this->invokeProperty($this->client, 'fp')), 'Unknown');
	}
	
}

<?php
namespace Wbaiyy\Tests;

use Wbaiyy\YiiPredis\Connection;
use Predis\Client;

class ConnectionTest extends TestCase
{
    /**
     * @var Client
     */
    private $predis;

    public function setUp()
    {
        parent::setUp();
        $this->predis = new Connection($GLOBALS['PREDIS_CONFIG']);
    }

    public function testExecuteCommand()
    {
        $this->predis->executeCommand('DEL', ['key']);
        $this->assertFalse((bool) $this->predis->exists('key'));
    }

    public function testGetClient()
    {
        $this->assertInstanceOf(Client::class, $this->predis->getClient());
    }

    public function testClose()
    {
        $this->predis->executeCommand('DEL', ['key']);
        $this->predis->close();
        $this->assertFalse($this->predis->getIsActive());
    }

    public function testGetDriverName()
    {
        $this->assertEquals('predis', $this->predis->getDriverName());
    }
}

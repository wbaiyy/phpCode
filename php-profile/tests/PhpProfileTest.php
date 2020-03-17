<?php
namespace Wbaiyy\Tests;

use Wbaiyy\PhpProfile\AbstractXhprof;
use Wbaiyy\PhpProfile\PhpProfile;
use Wbaiyy\PhpProfile\Uprofiler;
use Wbaiyy\PhpProfile\Xhprof;

class PhpProfileTest extends TestCase
{
    /**
     * @var PhpProfile
     */
    private $profile;

    public function setUp()
    {
        parent::setUp();
        $this->profile = new PhpProfile('phpunit', true, new MockXhprof());
    }

    public function tearDown()
    {
        $this->invokeStaticProperty(PhpProfile::class, 'started', false);
        parent::tearDown();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(
            PHP_VERSION_ID > 70000 ? Xhprof::class : Uprofiler::class,
            $this->invokeProperty(new PhpProfile('phpunit', true), 'xhprof')
        );


        $this->assertInstanceOf(
            MockXhprof::class,
            $this->invokeProperty(new PhpProfile('phpunit', true, new MockXhprof()), 'xhprof')
        );
    }

    public function testStart()
    {
        $this->assertFalse($this->profile->start());

        $profile = new PhpProfile('phpunit', true, new MockXhprof(true));
        $_GET[PhpProfile::TRIGGER_NAME] =1 ;
        $this->assertTrue($profile->start());
    }

    public function testStop()
    {
        $profile = new PhpProfile('phpunit', true, new MockXhprof(true));
        $this->assertFalse($profile->stop());

        $this->invokeStaticProperty(PhpProfile::class, 'started', true);
        $this->assertTrue(is_int($profile->stop()));

        $profile->minPageTime = 10;
        $this->assertFalse($profile->stop());

    }

    public function testInitSampling()
    {
        $this->assertSame(1, $this->invokeMethod($this->profile, 'initSampling'));

        $this->invokeProperty($this->profile, 'isDebug', false);
        $this->assertSame(1000, $this->invokeMethod($this->profile, 'initSampling'));

        $_SERVER['ENV'] = 'testing';
        $this->assertSame(10, $this->invokeMethod($this->profile, 'initSampling'));
    }

    public function testSaveByDebug()
    {
        $this->assertSame(0, $this->invokeMethod($this->profile, 'saveByDebug', [[]]));
    }

    public function testGetIp()
    {
        $this->assertSame('0.0.0.0', $this->invokeMethod($this->profile, 'getIp'));

        $_SERVER['REMOTE_ADDR'] = '127.0.0.2';
        $this->assertSame('127.0.0.2', $this->invokeMethod($this->profile, 'getIp'));

        $_SERVER['HTTP_TRUE_CLIENT_IP'] = '127.0.0.1';
        $this->assertSame('127.0.0.1', $this->invokeMethod($this->profile, 'getIp'));
    }

    public function testDisable()
    {
        $profile = new PhpProfile('phpunit', true, new MockXhprof(true));
        $this->assertNotEmpty($this->invokeMethod($profile, 'disable'));

        $profile->minFunctionTime = 10;
        $this->assertEmpty($this->invokeMethod($profile, 'disable'));
    }
}

class MockXhprof extends AbstractXhprof
{
    private $enable;

    public function __construct($enable = false)
    {
        $this->enable = $enable;
    }

    public function isEnable()
    {
        return $this->enable;
    }

    public function enable()
    {
        return true;
    }

    public function disable()
    {
        return [
            ['wt' => 1000000]
        ];
    }
}

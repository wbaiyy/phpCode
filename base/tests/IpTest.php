<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Ip;

class IpTest extends TestCase
{
    const LAN = '192.168.3.1';
    const BEIJING = '58.135.125.55';
    const SHENZHEN = '58.251.136.62';
    const US = '50.31.193.60';

    public function testGet()
    {
        $ip = new Ip();
        $this->invokeProperty($ip, 'ip', null);
        $_SERVER['REMOTE_ADDR'] = static::LAN;
        $this->assertEquals(static::LAN, $ip->get());

        $this->invokeProperty($ip, 'ip', null);
        $ip->searches = 'HTTP_X_FORWARDED_FOR,REMOTE_ADDR';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '0'; // invalid
        $_SERVER['REMOTE_ADDR'] = static::LAN;
        $this->assertEquals(static::LAN, $ip->get());
        $this->assertEquals(
            sprintf('%u', ip2long(static::LAN)),
            $ip->get(true)
        );

        $this->invokeProperty($ip, 'ip', null);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = static::US; // 优先
        $_SERVER['REMOTE_ADDR'] = static::SHENZHEN;
        $this->assertEquals(static::US, $ip->get());
        $this->assertEquals(
            sprintf('%u', ip2long(static::US)),
            $ip->get(true)
        );

        // 多ip，只获取第一个
        $this->invokeProperty($ip, 'ip', null);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = static::SHENZHEN . ',' . static::BEIJING;
        $this->assertEquals(static::SHENZHEN, $ip->get());

        // invalid ip
        $this->invokeProperty($ip, 'ip', null);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.256';
        $this->assertEquals(0, $ip->get(true));
    }
}

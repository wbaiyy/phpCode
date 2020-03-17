<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Datetime;
use Wbaiyy\Base\Exceptions\UnknownPropertyException;

class DatetimeTest extends TestCase
{
    public function testMagicGet()
    {
        $datetime = new Datetime();
        $this->assertEquals(
            $datetime->timezones['cn'],
            $datetime->cn->getTimezone()->getName()
        );
        $this->assertEquals(
            $datetime->timezones['us'],
            $datetime->us->getTimezone()->getName()
        );
        $this->assertEquals(
            $datetime->timezones['utc'],
            $datetime->utc->getTimezone()->getName()
        );

        $this->expectException(UnknownPropertyException::class);
        $datetime->notfound;
    }

    public function testGet()
    {
        $datetime = new Datetime();
        $this->assertEquals(
            $datetime->timezones['cn'],
            $datetime->get($datetime->timezones['cn'])->getTimezone()->getName()
        );
        $this->assertEquals(
            $datetime->timezones['cn'],
            $datetime->get('cn')->getTimezone()->getName()
        );
    }
}

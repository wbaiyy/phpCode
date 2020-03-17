<?php
namespace ego\tests\helper;

use ego\helpers\Arr;
use ego\helpers\Str;
use Wbaiyy\Base\Exceptions\UnknownPropertyException;
use ego\tests\TestCase;

class HelperTest extends TestCase
{
    public function testMagicGet()
    {
        $this->assertInstanceOf(Arr::class, app()->helper->arr);
        $this->assertInstanceOf(Str::class, app()->helper->str);

        $this->expectException(UnknownPropertyException::class);
        app()->helper->__get('notfound');
    }

    public function testGetHelper()
    {
        $this->assertInstanceOf(
            Arr::class,
            $this->invokeMethod(app()->helper, 'getHelper', ['arr'])
        );
        // isset
        $this->assertInstanceOf(
            Arr::class,
            $this->invokeMethod(app()->helper, 'getHelper', ['arr'])
        );
        $this->assertInstanceOf(
            Str::class,
            $this->invokeMethod(app()->helper, 'getHelper', ['str'])
        );
        $this->assertNull($this->invokeMethod(app()->helper, 'getHelper', ['notfound']));
    }
}

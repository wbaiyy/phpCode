<?php
namespace ego\tests\base;

use app\services\goods\GoodsService;
use ego\base\ServiceLocator;
use ego\tests\TestCase;
use yii\base\InvalidParamException;
use yii\base\UnknownPropertyException;
use app\modules\goods\components\GoodsComponent;

class ServiceLocatorTest extends TestCase
{
    public function test__get()
    {
        $this->assertInstanceOf(GoodsComponent::class, app()->getModule('goods')->__get('GoodsComponent'));
        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessageRegExp('/test/');
        app()->getModule('goods')->__get('test');
    }

    public function testIsSupportedClassSuffix()
    {
        $this->assertTrue(ServiceLocator::isSupportedClassSuffix('UserComponent'));
        $this->assertTrue(ServiceLocator::isSupportedClassSuffix('UserService'));
        $this->assertFalse(ServiceLocator::isSupportedClassSuffix('userComponent'));
        $this->assertFalse(ServiceLocator::isSupportedClassSuffix('UserSuffix'));
    }

    public function testGetByCalledClass()
    {
        $this->assertInstanceOf(
            GoodsComponent::class,
            $this->invokeMethod(
                app()->getModule('goods'),
                'getByCalledClass',
                ['GoodsComponent', get_class(app()->getModule('goods'))]
            )
        );

        $this->assertInstanceOf(
            GoodsService::class,
            $this->invokeMethod(
                app()->getModule('goods'),
                'getByCalledClass',
                ['GoodsService', get_class(app()->getModule('goods'))]
            )
        );
    }

    public function testGetSubNamespaceByClassName()
    {

        $this->assertEquals(
            'components\GoodsComponent',
            $this->invokeMethod(app()->getModule('goods'), 'getSubNamespaceByClassName', ['GoodsComponent'])
        );
        // isset
        $this->assertEquals(
            'components\GoodsComponent',
            $this->invokeMethod(app()->getModule('goods'), 'getSubNamespaceByClassName', ['GoodsComponent'])
        );

        $this->expectException(InvalidParamException::class);
        $this->expectExceptionMessageRegExp('/不支持的类名/');
        $this->invokeMethod(app()->getModule('goods'), 'getSubNamespaceByClassName', ['test']);
    }
}

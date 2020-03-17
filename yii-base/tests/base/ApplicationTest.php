<?php
namespace ego\tests\base;

use app\modules\user\Module;
use yii;
use yii\base\InvalidParamException;
use ego\tests\TestCase;
use app\modules\goods\Module as GoodsModule;
use app\modules\user\Module as UserModule;

class ApplicationTest extends TestCase
{
    public function test__get()
    {
        $this->assertInstanceOf(GoodsModule::class, app()->Goods);
        $this->assertInstanceOf(UserModule::class, app()->User);
        $this->expectException(yii\base\UnknownPropertyException::class);
        $this->expectExceptionMessageRegExp('/test/');
        app()->__get('test');
    }

    public function testLoadConfig()
    {
        $configFile = Yii::getAlias('@app/config/web.php');
        $config = app()->loadConfig($configFile);
        $this->assertTrue($config['params']['phpunit']);

        $config = app()->loadConfig($configFile, 'product');
        $this->assertFalse(isset($config['params']['phpunit']));

        $this->expectException(InvalidParamException::class);
        $this->expectExceptionMessageRegExp('/配置文件.+不存在/');
        app()->loadConfig($configFile . 'a');
    }

    public function testGetDeveloper()
    {
        $this->assertNull(app()->getDeveloper());

        $_SERVER['DEVELOPER'] = 'foo';
        $this->assertEquals('foo', app()->getDeveloper());

        $_SERVER['DEVELOPER'] = 3;
        $this->assertEquals(3, app()->getDeveloper());

        $_SERVER['SERVER_NAME'] = 'www.gearbest.com.foo.dev72.egocdn.com';
        $this->assertEquals('foo', app()->getDeveloper());
    }

    public function testGetModule()
    {
        $this->assertInstanceOf(Module::class, app()->getModule('user'));

        $this->assertNull(app()->getModule('debug'));
        app()->setModule('yii-debug', 'yii\debug\Module');
        $this->assertInstanceOf('yii\debug\Module', app()->getModule('debug'));
    }

    public function testGetServiceLocator()
    {
        $this->assertNull($this->invokeMethod(app(), 'getServiceLocator', ['test']));
        $this->assertNull($this->invokeMethod(app(), 'getServiceLocator', ['user']));
        $this->assertInstanceOf(
            UserModule::class,
            $this->invokeMethod(app(), 'getServiceLocator', ['User'])
        );
        $this->assertInstanceOf(
            UserModule::class,
            $this->invokeProperty(app(), 'serviceLocators.User')
        );
        // string
        $this->invokeProperty(app(), 'serviceLocators.User', 'stdClass');
        $this->assertInstanceOf(
            'stdClass',
            $this->invokeMethod(app(), 'getServiceLocator', ['User'])
        );
    }
}

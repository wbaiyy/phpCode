<?php
namespace Wbaiyy\Tests;


use Wbaiyy\Base\Env;

class EnvTest extends TestCase
{
    public function testToString()
    {
        $env = new Env();
        $this->assertSame('product', $env . '');

        $env = new Env(['env' => 'test']);
        $this->assertSame('test', $env . '');
    }

    public function isDevProvider()
    {
        return [
            [false, 'test'],
            [false, 'product'],
            [false, 'debug'],

            [true, 'dev'],
            [true, 'dev-home'],
            [true, 'dev-vbox'],
        ];
    }
    /**
     * @dataProvider isDevProvider
     */
    public function testIsDev($expected, $env)
    {
        $this->assertSame($expected, (new Env(['env' => $env]))->isDev());
    }

    public function isTestProvider()
    {
        return [
            [false, 'dev'],
            [false, 'product'],

            [true, 'test'],
            [true, 'test-home'],
            [true, 'test-vbox'],
        ];
    }
    /**
     * @dataProvider isTestProvider
     */
    public function testIsTest($expected, $env)
    {
        $this->assertSame($expected, (new Env(['env' => $env]))->isTest());
    }

    public function isProductProvider()
    {
        return [
            [false, 'dev'],
            [false, 'test'],

            [true, 'product'],
            [true, 'product-home'],
            [true, 'product-vbox'],
        ];
    }

    /**
     * @dataProvider isProductProvider
     */
    public function testIsProduct($expected, $env)
    {
        $this->assertSame($expected, (new Env(['env' => $env]))->isProduct());
    }

    public function testIsPreRelease()
    {
        $this->assertFalse(Env::isPreRelease());
        $_COOKIE['staging'] = 'false';
        $this->assertFalse(Env::isPreRelease());

        $_COOKIE['staging'] = 'true';
        $this->assertTrue(Env::isPreRelease());
    }

    public function isLocalProvider()
    {
        return [
            [false, 'product'],

            [true, 'dev'],
            [true, 'test'],
        ];
    }

    /**
     * @dataProvider isLocalProvider
     */
    public function testIsLocal($expected, $env)
    {
        $this->assertSame($expected, (new Env(['env' => $env]))->isLocal());
    }

    public function isPhpunitProvider()
    {
        return [
            [false, 'dev'],
            [false, 'test'],

            [true, 'phpunit'],
            [true, 'phpunit-home'],
            [true, 'phpunit-vbox'],
        ];
    }

    /**
     * @dataProvider isPhpunitProvider
     */
    public function testIsPhpunit($expected, $env)
    {
        $this->assertSame($expected, (new Env(['env' => $env]))->isPhpunit());
    }
}

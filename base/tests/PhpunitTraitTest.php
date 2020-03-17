<?php
namespace Wbaiyy\Tests;

class PhpunitTraitTest extends TestCase
{
    public function testInvokeMethod()
    {
        $user = new PhpunitTraitUser();
        $this->assertNull($this->invokeProperty($user, 'name'));

        $this->invokeMethod($user, 'setName', ['foo']);
        $this->assertSame('foo', $this->invokeProperty($user, 'name'));
    }

    public function testInvokeProperty()
    {
        $user = new PhpunitTraitUser();
        $this->assertNull($this->invokeProperty($user, 'name'));

        $this->invokeProperty($user, 'name', 'foo');
        $this->assertSame('foo', $this->invokeProperty($user, 'name'));
    }

    public function testInvokeStaticMethod()
    {
        $user = new PhpunitTraitUser();
        $this->invokeStaticProperty($user, 'email', null);

        $this->invokeStaticMethod($user, 'setEmail', ['foobar@example.com']);
        $this->assertSame('foobar@example.com', $this->invokeStaticProperty($user, 'email'));
    }

    public function testInvokeStaticProperty()
    {
        $user = new PhpunitTraitUser();
        $this->invokeStaticProperty($user, 'email', null);
        $this->assertNull($this->invokeStaticProperty($user, 'email'));

        $this->invokeProperty($user, 'email', PhpunitTraitUser::EMAIL);
        $this->assertSame(PhpunitTraitUser::EMAIL, $this->invokeStaticProperty($user, 'email'));
    }
}

class PhpunitTraitUser
{
    const EMAIL = 'foo@example.com';
    private $name;
    private static $email;

    private function setName($name)
    {
        $this->name = $name;
        static::$email = 'foo@example.com';
    }

    private static function setEmail($email)
    {
        static::$email = $email;
        return static::$email;
    }
}

<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Component;
use Wbaiyy\Base\Exceptions\UnknownPropertyException;

class ComponentTest extends TestCase
{
    public function testConfigure()
    {
        $user = new ComponentUser(['name' => 'foo']);
        $this->assertEquals('foo', $user->name);
        Component::configure($user, ['name' => 'bar']);
        $this->assertEquals('bar', $user->name);
    }

    public function testMagicGet()
    {
        $user = new ComponentUser(['email' => 'foo@example.com']);
        $this->assertEquals('foo@example.com', $user->email);

        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessage(
            '获取未知属性: ' . ComponentUser::class . '::password'
        );
        $user->password;
    }

    public function testMagicSet()
    {
        $user = new ComponentUser(['email' => 'foo@example.com']);
        $this->assertEquals('foo@example.com', $user->email);

        $user->email = 'bar@example.com';
        $this->assertEquals('bar@example.com', $user->email);

        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessage(
            '设置未知属性: ' . ComponentUser::class . '::password'
        );
        $user->password = '123456';
    }
}

/**
 * @property string $email
 * @property string $password;
 */
class ComponentUser extends Component
{
    public $name;
    protected $email;
    protected $password;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
}

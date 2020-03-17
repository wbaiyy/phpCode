<?php
namespace Wbaiyy\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * 执行一个对象的`private`或者`protected`方法
     *
     * @param mixed $object
     * @param string $method
     * @param array $args
     * @return mixed
     */
    protected function invokeMethod($object, $method, array $args = [])
    {
        $m = (new \ReflectionClass($object))->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($object, $args);
    }

    /**
     * 获取或者设置一个对象`private`或者`protected`属性
     *
     * @param string $class
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function invokeProperty($class, $name, $value = null)
    {
        $property = new \ReflectionProperty($class, $name);
        $property->setAccessible(true);

        if (2 === func_num_args()) {
            return $property->getValue($class);
        }
        $property->setValue($class, $value);
        return $class;
    }
}

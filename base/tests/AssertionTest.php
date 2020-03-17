<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Assertion;
use Wbaiyy\Base\Exceptions\AssertionException;
use Wbaiyy\Base\Str;

class AssertionTest extends TestCase
{
    public function testAssertTrue()
    {
        // true
        Assertion::assertTrue(true);

        ob_start();
        // message
        try {
            Assertion::assertTrue(false, 'assert failed');
        } catch (AssertionException $e) {
            echo $e->getMessage();
        }

        // message -> null
        try {
            $line = __LINE__;
            Assertion::assertTrue(false);
        } catch (AssertionException $e) {
            echo $e->getMessage();
        }

        $this->assertTrue(Str::has(
            ob_get_clean(),
            ['assert failed', '断言true失败']
        ));

        /** @var AssertionException $e */
        /** @var int $line */
        $this->assertEquals(__FILE__, $e->getFile());
        $this->assertEquals($line + 1, $e->getLine());
    }
}

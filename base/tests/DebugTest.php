<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Debug;
use Wbaiyy\Base\Str;

class DebugTest extends TestCase
{
    /**
     * @var Debug
     */
    private $debug;

    public function setUp()
    {
        parent::setUp();
        $this->debug = new Debug(['isDebug' => true]);
    }

    public function dataProvider()
    {
        return [
            [get_defined_constants(true)['user'], 'aaaa', 'bbbb', md5(time())],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDump($userConstants, $a, $b, $c)
    {
        ob_start();
        $this->debug->dump($userConstants, $a, $b, $c);
        $this->assertTrue(Str::has(
            ob_get_clean(),
            [$a, $b, $c, basename(__FILE__) . ' : ' . (__LINE__ - 3)]
        ));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExport($userConstants, $a, $b, $random)
    {
        ob_start();
        $this->debug->export($userConstants, $a, $b, $random);
        $output = ob_get_clean();
        $this->assertNotFalse(strpos(
            $output,
            basename(__FILE__) . ' : ' . (__LINE__ - 4)
        ));
    }
}
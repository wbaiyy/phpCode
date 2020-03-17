<?php
namespace Wbaiyy\Tests;

use Wbaiyy\PhpProfile\Xhprof;

/**
 * @requires extension xhprof
 */
class XhprofTest extends TestCase
{
    public function testAll()
    {
        $xhprof = new Xhprof();
        $xhprof->enable();
        $this->assertNotEmpty($xhprof->disable());
    }
}

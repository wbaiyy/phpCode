<?php
namespace Wbaiyy\Tests;

use Wbaiyy\PhpProfile\Uprofiler;

/**
 * @requires extension uprofiler
 */
class UprofilerTest extends TestCase
{
    public function testAll()
    {
        $xhprof = new Uprofiler();
        $xhprof->enable();
        $this->assertNotEmpty($xhprof->disable());
    }
}

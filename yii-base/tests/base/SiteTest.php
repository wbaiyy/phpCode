<?php
namespace ego\tests\base;

use ego\tests\TestCase;

class SiteTest extends TestCase
{
    public function testGetName()
    {
        $this->assertEquals('GLB', app()->site->code);
        $this->assertEquals('girlbest', app()->site->name);
        $this->assertNull(app()->site->getName('nofound'));
    }
}

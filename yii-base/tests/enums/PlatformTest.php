<?php
namespace ego\tests\enums;

use ego\tests\TestCase;
use ego\enums\Platform;

class PlatformTest extends TestCase
{
    public function testGetName()
    {
        $this->assertEquals('PC', Platform::getName(Platform::PC));
        $this->assertEquals('unknown', Platform::getName(-1));
    }

    public function testGetNames()
    {
        $this->assertEquals(
            Platform::getName(Platform::PC) . '、' . Platform::getName(Platform::ANDROID),
            Platform::getNames([Platform::PC, Platform::ANDROID])
        );

        $names = Platform::getNames([Platform::PC, Platform::ANDROID], null);
        $this->assertTrue(in_array(Platform::getName(Platform::PC), $names));
        $this->assertTrue(in_array(Platform::getName(Platform::ANDROID), $names));
    }
}
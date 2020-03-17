<?php
namespace ego\tests\base;

use ego\tests\TestCase;

class CryptTest extends TestCase
{
    public function testDecode()
    {
        $encode = app()->crypt->encode('test');
        $this->assertSame('test', app()->crypt->decode($encode));

        $key = time();
        $encode = app()->crypt->encode('test', 0, $key);
        $decode = app()->crypt->decode($encode, $key);
        $this->assertSame('test', $decode);
        $this->assertFalse(app()->crypt->decode($encode));

        // expires
        $encode = app()->crypt->encode('test', 10);
        $this->assertSame('test', app()->crypt->decode($encode));

        $encode = app()->crypt->encode('test', -10);
        $this->assertNull(app()->crypt->decode($encode));
    }
}

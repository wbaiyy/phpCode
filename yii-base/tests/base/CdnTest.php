<?php
namespace ego\tests\base;

use ego\tests\TestCase;

class CdnTest extends TestCase
{
    public function testClear()
    {
        $result = app()->cdn->clear(['/', 'test']);
        $this->assertArrayHasKey('/', $result);
        $this->assertArrayHasKey('test', $result);

        app()->cdn->api = 'http://' . time();
        $result = app()->cdn->clear('test');
        // cURL error 6: Could not resolve host: 1494570891test (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)
        $this->assertTrue(false !== strpos($result['test'], 'resolve host'));
    }
}

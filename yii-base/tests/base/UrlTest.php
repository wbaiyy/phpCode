<?php
namespace ego\tests\base;

use ego\tests\TestCase;
use ego\base\Url;

class UrlTest extends TestCase
{
    /** @var Url */
    protected $url;
    protected $homepage;
    protected $querystring = null;

    public function setUp()
    {
        parent::setUp();
        $this->url = new Url();
        $this->querystring = [
            'keyword' => 'foo',
            'date' => date('Y-m-d H:i:s'),
            'type' => range(1, 10),
            'foo' => 'php语言',
        ];
        $this->homepage = 'http://www.example.com';
    }

    public function testAppend()
    {
        // querystring -> null
        $this->assertEquals($this->homepage, $this->url->append($this->homepage, ''));
        $this->assertEquals($this->homepage, $this->url->append($this->homepage, null));

        // array
        $url = $this->homepage;
        $this->assertEquals(
            $url . '?' . http_build_query($this->querystring),
            $this->url->append($url, $this->querystring)
        );

        // &
        $querystring = http_build_query($this->querystring);
        $url = $this->homepage . '?123';
        $this->assertEquals(
            $url . '&' . $querystring,
            $this->url->append($url, $querystring)
        );
    }
}

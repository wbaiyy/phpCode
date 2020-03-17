<?php
namespace ego\tests\mail;

use ego\tests\TestCase;
use ego\mail\SmartyDbResource;
use yii\base\InvalidValueException;

class SmartyDbResourceTest extends TestCase
{
    /**
     * @var SmartyDbResource
     */
    protected $resource;

    public function setUp()
    {
        parent::setUp();
        $this->resource = new SmartyDbResource();
        $this->resource->setModel(new MailTemplateModel());
    }

    public function testFetch()
    {
        $source = null;
        $mtime = null;

        $this->resource->fetch('phpunit', $source, $mtime);
        $this->assertNotNull($source);
        $this->assertNotNull($mtime);
    }

    public function testGetMailTemplateData()
    {
        $this->assertNotEmpty($this->resource->getMailTemplateData('phpunit'));
        try {
            $this->resource->getMailTemplateData('phpunit_using_0');
        } catch (InvalidValueException $e) {
        }
        $this->assertTrue(isset($e) && false !== strpos($e->getMessage(), '未启用'));
        $this->expectException(InvalidValueException::class);
        $this->resource->getMailTemplateData('notfound');
    }
}

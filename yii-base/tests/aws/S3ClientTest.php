<?php
namespace ego\tests\aws;

use Aws\S3\S3Client;
use Aws\Result;
use ego\tests\TestCase;

class S3ClientTest extends TestCase
{
    protected $s3path = 'phpunit.test.xml';

    public function testPutObject()
    {
        //$result = app()->s3->putObject(__DIR__ . '/../phpunit.xml', $this->s3path);
        //$this->assertInstanceOf(Result::class, $result);

        $this->assertTrue(is_string(
            app()->s3->putObject('notfound', $this->s3path)
        ));

        $this->expectException('Exception');
        app()->s3->slient(false)->putObject('notfound', $this->s3path);
    }

    public function testDeleteObject()
    {
        $this->assertInstanceOf(Result::class, app()->s3->deleteObject($this->s3path));
        $this->invokeProperty(
            app()->s3,
            'client',
            new S3Client([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => [
                    'key' => 'xxx',
                    'secret' => 'yyy',
                ],
            ])
        );
        $this->assertTrue(is_string(
            app()->s3->deleteObject($this->s3path, $this->s3path)
        ));

        $this->expectException('Exception');
        app()->s3->slient(false)->deleteObject($this->s3path);
    }
}

<?php
namespace Wbaiyy\Tests;

use Wbaiyy\ComposerCleaner\Cleaner;

class CleanerTest extends TestCase
{
    /**
     * @var Cleaner
     */
    private $cleaner;

    public function setUp()
    {
        parent::setUp();
        $this->cleaner = new Cleaner(new IO(), new Filesystem());
    }

    public function testClean()
    {
        $this->expectOutputRegex('/return-true/');
        $this->cleaner->clean(
            __DIR__ . '/../vendor',
            [
                'test/return-true'
            ]
        );
    }

    public function testCleanPackage()
    {
        $this->assertFalse(
            $this->invokeMethod($this->cleaner, 'cleanPackage', [__DIR__ . '/../src'])
        );

        $this->expectOutputRegex('/return-true/');
        $this->assertTrue(
            $this->invokeMethod($this->cleaner, 'cleanPackage', [__DIR__])
        );
    }

    public function testGetInstalledPackages()
    {
        $this->assertNotEmpty(
            $this->invokeMethod($this->cleaner, 'getInstalledPackages', [__DIR__ . '/../vendor'])
        );

        $this->expectOutputRegex('/Composer installed file/');
        $this->assertEmpty(
            $this->invokeMethod($this->cleaner, 'getInstalledPackages', [__DIR__])
        );
    }

    public function testParseGitAttributes()
    {
        $files = $this->invokeMethod($this->cleaner, 'parseGitAttributes', [file(__DIR__ . '/.gitattributes')]);
        $this->assertCount(1, $files);
    }
}

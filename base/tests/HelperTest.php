<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Exceptions\InvalidArgumentException;
use Wbaiyy\Base\Exceptions\InvalidValueException;
use Wbaiyy\Base\Helper;

class HelperTest extends TestCase
{
    protected function getCalledBacktrace($level = 1)
    {
        return [
            'getCalledBacktrace' => Helper::getCalledBacktrace($level),
            'line' => __LINE__ - 1,
        ];
    }
    public function testGetCalledBacktrace()
    {
        $calledBacktrace = Helper::getCalledBacktrace(0);
        $this->assertEquals(__FILE__, $calledBacktrace->file);
        $this->assertSame(__LINE__ - 2, $calledBacktrace->line);

        $calledBacktrace = $this->getCalledBacktrace();
        $this->assertEquals(__FILE__, $calledBacktrace['getCalledBacktrace']->file);
        $this->assertSame(__LINE__ - 2, $calledBacktrace['getCalledBacktrace']->line);

        $calledBacktrace = $this->getCalledBacktrace(0);
        $this->assertEquals(__FILE__, $calledBacktrace['getCalledBacktrace']->file);
        $this->assertSame($calledBacktrace['line'], $calledBacktrace['getCalledBacktrace']->line);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('获取回溯跟踪错误');
        Helper::getCalledBacktrace();

    }


    public function isEmptyProvider()
    {
        return [
            [true, null],
            [true, ''],
            [true, []],
            [true, '     '],
            [false, 0],
        ];
    }
    /**
     * @dataProvider isEmptyProvider
     */
    public function testIsEmpty($expected, $value)
    {
        $this->assertSame($expected, Helper::isEmpty($value));
    }

    public function isScalarProvider()
    {
        return [
            [true, null],
            [true, ''],
            [true, 0],
            [true, 0.01],
            [false, []],
            [false, $this],
        ];
    }
    /**
     * @dataProvider isScalarProvider
     */
    public function testIsScalar($expected, $value)
    {
        $this->assertSame($expected, Helper::isScalar($value));
    }

    public function valueProvider()
    {
        return [
            [true, true],
            ['value', 'value'],
            [null, function() {}],
            ['value', function() { return 'value'; }],
            ['value', function($value) { return $value; }, ['value']],
        ];
    }
    /**
     * @dataProvider valueProvider
     */
    public function testValue($expected, $value, array $arguments = [])
    {
        $this->assertSame($expected, Helper::value($value, $arguments));
    }

    public function formatSizeProvider()
    {
        return [
            ['1023 Bytes', 1023, 0],
            ['1023.00 Bytes', 1023, 2],
            ['1.00 K', '1024', 2],
            ['1.27 K', 1024 * 1.267, 2],
            ['1.00 M', 1048576, 2],
            ['3.60 M', 1048576 * 3.6, 2],
            ['1.00 G', 1073741824, 2],
            ['1.020 G', 1073741824 * 1.015, 3],
            ['-1.020 G', -1073741824 * 1.015, 3],
            [sprintf('%.2f', filesize(__FILE__) / 1024) . ' K', __FILE__, 2],
            [null, 'noafile', null]
        ];
    }
    /**
     * @dataProvider formatSizeProvider
     */
    public function testFormatSize($expected, $size, $precision)
    {
        if (is_string($size) && null === $precision) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessageRegExp('/noafile/');
            Helper::formatSize($size);
        } else {
            $this->assertEquals(
                $expected,
                Helper::formatSize($size, $precision)
            );
        }
    }

    public function revertFormatSizeProvider()
    {
        return [
            [10345, 10345],
            [1023, '1023 B'],
            [1023, '1023 Bytes'],
            [10240, '10 K'],
            [1048576 * 3.6, '3.6 M'],
            [1048576 * 3.6, '3.6 m'],
            [1073741824, '1 G'],
            [-1, '16 mg'],
        ];
    }
    /**
     * @dataProvider revertFormatSizeProvider
     */
    public function testRevertFormatSize($expected, $size)
    {
        if (-1 == $expected) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessageRegExp('/16 m/');
            Helper::revertFormatSize($size);
        } else {
            $this->assertSame($expected, Helper::revertFormatSize($size));
        }
    }

    public function testArrayResult()
    {
        $this->assertEquals(
            'message',
            Helper::arrayResult(0, 'message', null)['message']
        );
    }

    public function testMicrotime()
    {
        $this->assertSame(6, strlen(explode('.', Helper::microtime())[1]));
    }
}

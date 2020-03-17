<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Exceptions\InvalidArgumentException;
use Wbaiyy\Base\Str;

class StrTest extends TestCase
{
    public function stringProvider()
    {
        return [[
            // string
            '判断某个字符串是否以指定字符串结尾
            *
            * @param string $string 原始字符串
            * @param string|array $ends 期望结尾的字符串，个或者多个取决于`$logic`
            * @param string $logic 当`$ends`为数组时，，否则同时匹配多个
            * @return bool*/',

            // start
            '判断',

            // contains
            '@param',

            // notFound
            'foo',

            // end
            'bool*/',
        ]];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testHas($string, $start, $contains, $notFound, $end)
    {
        $this->assertTrue(Str::has($string, [$start]));
        $this->assertTrue(Str::has($string, [$contains]));
        $this->assertFalse(Str::has($string, [$notFound]));
        $this->assertTrue(Str::has($string, [$end]));

        $this->assertTrue(Str::has(
            $string,
            [$start, $contains, $notFound, $end],
            '|'
        ));
        $this->assertTrue(Str::has(
            $string,
            [$start, $contains, $end]
        ));
        $this->assertFalse(Str::has(
            $string,
            [$start, $contains, $notFound, $end]
        ));
    }

    /**
     * @dataProvider stringProvider
     */
    public function testStartWith($string, $start, $contains, $notFound, $end)
    {
        $this->assertTrue(Str::startWith($string, [$start]));
        $this->assertFalse(Str::startWith($string, [$contains]));
        $this->assertFalse(Str::startWith($string, [$notFound]));
        $this->assertFalse(Str::startWith($string, [$end]));

        $this->assertTrue(Str::startWith(
            $string,
            [$contains, $notFound, $end, $start]
        ));
    }

    /**
     * @dataProvider stringProvider
     */
    public function testEndWith($string, $start, $contains, $notFound, $end)
    {
        $this->assertFalse(Str::endWith($string, [$start]));
        $this->assertFalse(Str::endWith($string, [$contains]));
        $this->assertFalse(Str::endWith($string, [$notFound]));
        $this->assertTrue(Str::endWith($string, [$end]));
        $this->assertTrue(Str::endWith(
            $string,
            [$start, $contains, $notFound, $end]
        ));
    }

    public function simpleRandomProvider()
    {
        return [
            [4, null],
            [6, 1],
            [8, 2],
            [7, 4],
            [5, 8],
            [3, 16],
            [4, 32],
            [9, 64],
        ];
    }

    /**
     * @dataProvider simpleRandomProvider
     */
    public function testSimpleRandom($len, $mode, $addChars = '')
    {
        $string = Str::random($len, $mode);
        $patten = preg_quote(Str::getRandomChars($mode), '/');
        $this->assertSame($len, strlen($string));
        $this->assertSame(
            1,
            preg_match("/^[{$patten}]+$/", $string), $string . ', ' . $patten
        );
    }

    public function testComplexRandom()
    {
        // invalid mode
        $invalidModes = [0, -1, Str::MAX_XOR + 1];
        ob_start();
        foreach ($invalidModes as $mode) {
            try {
                Str::random(1, $mode);
            } catch (InvalidArgumentException $e) {
                echo $e->getMessage();
            }
        }
        $this->assertSame(
            count($invalidModes),
            substr_count(ob_get_clean(), '不支持的字符串模式')
        );

        // xor
        $mode = Str::NUMERIC | Str::UPPER;
        for ($i = 0; $i < 5; $i++) {
            $string = Str::random(4, $mode);
            $patten = '/^[' . Str::getRandomChars($mode) . ']+$/';
            $this->assertSame(
                1,
                preg_match($patten, $string), $string . ', ' . $patten
            );
        }

        // addChars
        $string = Str::random(14, Str::NUMERIC, 'ABCD');
        $this->assertSame(4, preg_match_all('/[ABCD]/', $string));
    }

    public function testGetRandomChars()
    {
        // invalid mode
        $invalidModes = [0, -1, Str::MAX_XOR + 1];
        ob_start();
        foreach ($invalidModes as $mode) {
            try {
                Str::getRandomChars($mode);
            } catch (InvalidArgumentException $e) {echo $mode;
                echo $e->getMessage();
            }
        }
        $this->assertCount(
            substr_count(ob_get_clean(), '不支持的字符串模式'),
            $invalidModes
        );

        // simple
        $loop = [
            Str::LETTER,
            Str::LOWER,
            Str::UPPER,
            Str::NUMERIC,
            Str::ALPHANUMERIC,
            Str::EXTENDED,
            Str::SPECIALCHARS,
        ];
        foreach ($loop as $mode) {
            $this->assertSame(
                Str::RANDOM_CHARS[$mode],
                Str::getRandomChars($mode)
            );
        }

        // xor
        $mode = Str::NUMERIC | Str::UPPER | Str::SPECIALCHARS;
        $this->assertSame(
            Str::RANDOM_CHARS[Str::UPPER] . Str::RANDOM_CHARS[Str::NUMERIC] . Str::RANDOM_CHARS[Str::SPECIALCHARS],
            Str::getRandomChars($mode)
        );
    }

    public function toLowerCamelCaseProvider()
    {
        return [
            ['abc-def-ghi', 'abcDefGhi'],
            ['abc-def-ghi', 'abcDefGhi'],
            ['Abc-def-  ghi', 'abcDefGhi'],
            ['-abc-def-  ghi', 'abcDefGhi'],
            ['abc def ghi', 'abcDefGhi'],
            ['a-b-c', 'aBC'],
            ['Abc', 'abc']
        ];
    }

    /**
     * @dataProvider toLowerCamelCaseProvider
     */
    public function testToLowerCamelCase($string, $expected)
    {
        $this->assertSame($expected, Str::toLowerCamelCase($string));
    }

    public function toUpperCamelCaseProvider()
    {
        return [
            ['abc-def-ghi', 'AbcDefGhi'],
            ['abc-def-ghi', 'AbcDefGhi'],
            ['abc-def-  ghi', 'AbcDefGhi'],
            ['-abc-def-  ghi', 'AbcDefGhi'],
            ['abc def ghi', 'AbcDefGhi'],
            ['a-b-c', 'ABC'],
            ['abc', 'Abc']
        ];
    }

    /**
     * @dataProvider toUpperCamelCaseProvider
     */
    public function testToUpperCamelCase($string, $expected)
    {
        $this->assertSame($expected, Str::toUpperCamelCase($string));
    }

    public function revertCamelCaseProvider()
    {
        return [
            ['AbcDefGhi', 'abc-def-ghi'],
            ['AbcDefGhi', 'abc-def-ghi'],
            ['AbcDefGhi', 'abc def ghi', ' '],
            ['ABC', 'a-b-c'],
        ];
    }

    /**
     * @dataProvider revertCamelCaseProvider
     */
    public function testRevertCamelCase($string, $expected, $separator = '-')
    {
        $this->assertSame(
            $expected,
            Str::revertCamelCase($string, $separator)
        );
        // isset
        $this->assertSame(
            $expected,
            Str::revertCamelCase($string, $separator)
        );
    }

    public function substrProvider()
    {
        return [
            ['substrProvider', 'substrProvider', 100],
            ['substr', 'substrProvider', 6],
            ['', '中国人', 2],
            ['中', '中国人', 3],
            ['中', '中国人', 4],
            ['中', '中国人', 5],
            ['中国', '中国人', 7],
            ['中国...', '中国人', 7, '...'],
        ];
    }

    /**
     * @dataProvider substrProvider
     */
    public function testSubstr($expected, $string, $length, $append = '')
    {
        $this->assertSame(
            $expected,
            Str::substr($string, $length, $append)
        );
    }

    public function testFormat()
    {
        $this->assertEquals(
            'name: {name}, age: {age}',
            Str::format('name: {name}, age: {age}', [])
        );

        $this->assertEquals(
            'name: name, age: age',
            Str::format(
                'name: {name}, age: {age}',
                ['name' => 'name', 'age' => 'age']
            )
        );

        $this->assertEquals(
            'name: name, age: {age}',
            Str::format(
                'name: {$name}, age: {age}',
                ['name' => 'name', 'age' => 'age'],
                '{$'
            )
        );

        $this->assertEquals(
            'name: name, age: age',
            Str::format('name: {0}, age: {1}', ['name', 'age'])
        );
    }

    public function name2titleProvider()
    {
        return [
            ['ctypealnum', 'ctypealnum'],
            ['about-us', 'About Us'],
            ['android-3-5', 'Android 3.5', ],
            ['Android-3-5', 'Android 3.5', '', true],
            ['android-3.5', 'Android 3.5', '.'],
            ['android-3.5', 'Android 3.5', '.'], // isset
        ];
    }

    /**
     * @dataProvider name2titleProvider
     */
    public function testName2title($expected, $name, $extra = '', $ucwords = false)
    {
        $this->assertEquals($expected, Str::name2url($name, $extra, $ucwords));
        // isset
        $this->assertEquals($expected, Str::name2url($name, $extra, $ucwords));
    }
}
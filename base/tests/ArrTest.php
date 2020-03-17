<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\Arr;
use Wbaiyy\Base\ArrayAccess;
use Wbaiyy\Base\Exceptions\InvalidArgumentException;

class ArrTest extends TestCase
{
    private $arrData = [
        'local' => [
            'db' => [
                'username' => 'local user',
                'password' => 'local password',
                'port' => null,
            ],
            'foo' => 'foo',
        ],
        'name' => 'foo',
        'local.name' => 'local name',
        'not array' => false,
        'nil' => null,
        'info' => [
            'user_id' => 1,
            'username' => 'foo',
            'email' => 'foo@example.com',
            'password' => '123456',
        ],
    ];

    private $mergeA = [
        'db' => [
            'connections' => [
                'default' => [
                    'host' => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'dbname' => 'test',
                    'port' => 3306,
                    'driverOptions' => [
                        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
                    ],
                ],
            ],
        ],
        1 => [
            'one',
            'two',
        ],
    ];
    private $mergeB = [
        'db' => [
            'connections' => [
                'default' => [
                    'host' => '127.0.0.1',
                    'password' => 'root',
                    'driverOptions' => [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    ],
                ],
                'slave' => [
                    'host' => 'slave host',
                ]
            ],
        ],
        1 => [
            'three',
        ]
    ];

    public function testMerge()
    {
        // simple
        $array = Arr::merge([], $this->mergeA);
        $this->assertSame($this->mergeA, $array);

        // $argA -> array, preserveNumericKeys -> false
        $array = Arr::merge($this->mergeA, $this->mergeB);
        $this->assertEquals('127.0.0.1', $array['db']['connections']['default']['host']);
        $this->assertEquals('root', $array['db']['connections']['default']['password']);
        $this->assertEquals('slave host', $array['db']['connections']['slave']['host']);

        // numeric key
        $this->assertCount(3, $array['db']['connections']['default']['driverOptions']);
        $this->assertSame('three', $array[2][0]);

        // preserveNumericKeys -> true
        $array = Arr::merge(
            true,
            $this->mergeA,
            $this->mergeB,
            ['more' => 'more']
        );
        $this->assertSame('more', $array['more']);

        // numeric key
        $this->assertCount(2, $array['db']['connections']['default']['driverOptions']);
        $this->assertSame('three', $array[1][0]);
        $this->assertFalse(isset($array[2]));
    }

    public function getProvider()
    {
        return [
            // key = null
            [$this->arrData, $this->arrData, null],

            // array_key_exists
            [$this->arrData['name'], $this->arrData, 'name'],
            [$this->arrData['local.name'], $this->arrData, 'local.name'],
            [null, $this->arrData, 'nil'],

            // not set
            [null, $this->arrData, 'not set', null],
            ['default', $this->arrData, 'not set', 'default'],

            // .
            [$this->arrData['local']['db']['username'], $this->arrData, 'local.db.username'],
            [null, $this->arrData, 'local.not set.username'],
            ['default', $this->arrData, 'local.db.not set', 'default'],

            // not array
            [null, $this->arrData, 'not array.test'],
            [null, $this->arrData, 'local.db.username.test'],
        ];
    }

    /**
     * @dataProvider getProvider
     */
    public function testGet($expected, $array, $key = null, $default = null)
    {
        $this->assertSame(
            $expected,
            Arr::get($array, $key, $default)
        );
    }

    public function testSet()
    {
        $data = [
            'info' => [
                'name'  => 'foo',
                'email' => [
                    'foo' => 'foo@example.com',
                ],
            ],
            'a.b' => 'a.b'
        ];
        $array = $data;

        // key -> null
        $this->assertSame([], Arr::set($array, null, []));


        // allowDotNotation -> fasle
        $array = $data;
        Arr::set($array, 'a.b', 'value');
        $this->assertEquals('value', $array['a.b']);

        $array = $data;
        Arr::set($array, 'a.b', 'value');
        $this->assertEquals('value', $array['a.b']);

        $array = $data;
        Arr::set($array, 'info.name', 'value');
        $this->assertEquals('value', $array['info']['name']);
        $this->assertFalse(array_key_exists('info.name', $array));

        Arr::set($array, 'info.email.foo', 'value');
        $this->assertEquals('value', $array['info']['email']['foo']);
        $this->assertFalse(array_key_exists('info.email.foo', $array));

        // info.email.foo -> string
        Arr::set($array, 'info.email.foo.bar', 'value');
        $this->assertEquals('value', $array['info']['email']['foo']['bar']);
    }

    public function testFirst()
    {
        $this->assertEquals('value', Arr::first(['key' => 'value', 'bar']));
    }

    public function testLast()
    {
        $this->assertEquals('bar', Arr::last(['key' => 'value', 'bar']));
    }

    public function testFlatten()
    {
        $allowMimeExtensions = [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'msoffice' => [
                'excel' => ['xls', 'xlsx'],
                'word' => ['doc', 'docs'],
            ],
            'duplicate' => 'jpg',
        ];
        $this->assertEquals(
            [
                0 => 'jpg',
                1 => 'jpeg',
                2 => 'png',
                3 => 'gif',
                4 => 'xls',
                5 => 'xlsx',
                6 => 'doc',
                7 => 'docs',
                8 => 'jpg',
            ],
            Arr::flatten($allowMimeExtensions)
        );
        $this->assertEquals(
            [
                0 => 'xls',
                1 => 'xlsx',
                2 => 'doc',
                3 => 'docs',
            ],
            Arr::flatten($allowMimeExtensions['msoffice'])
        );
    }

    public function excludeProvider()
    {
        return [
            // case sensitive
            [
                [
                    'user_id' => 1,
                    'username' => 'foo',
                    'email' => 'foo@example.com',
                    'password' => '123456',
                ],
                $this->arrData['info'],
                'Username',
            ],
            // single
            [
                [
                    'user_id' => 1,
                    'username' => 'foo',
                    'password' => '123456',
                ],
                $this->arrData['info'],
                'email',
            ],
            // multi
            [
                [
                    'username' => 'foo',
                    'password' => '123456',
                ],
                $this->arrData['info'],
                'user_id, email',
            ],
            // array
            [
                [
                    'username' => 'foo',
                    'password' => '123456',
                ],
                $this->arrData['info'],
                ['user_id', 'email'],
            ],
        ];
    }

    /**
     * @dataProvider excludeProvider
     */
    public function testExclude($expected, $array, $keys)
    {
        $this->assertSame($expected, Arr::exclude($array, $keys));
    }

    public function pickProvider()
    {
        return [
            // case sensitive
            [
                [],
                $this->arrData['info'],
                'Username',
            ],
            // single
            [
                [
                    'user_id' => 1,
                ],
                $this->arrData['info'],
                'user_id',
            ],
            // multi
            [
                [
                    'username' => 'foo',
                    'password' => '123456',
                ],
                $this->arrData['info'],
                'username, password',
            ],
            // array
            [
                [
                    'username' => 'foo',
                    'password' => '123456',
                ],
                $this->arrData['info'],
                ['password', 'username'],
            ],
        ];
    }

    /**
     * @dataProvider pickProvider
     */
    public function testPickFields($expected, $array, $keys)
    {
        $this->assertSame($expected, Arr::pick($array, $keys));
    }

    public function testRename()
    {
        $array = ['username' => 'foo', 'email' => 'foo@example.com'];
        $array = Arr::rename($array, ['username' => 'name']);
        $this->assertCount(2, $array);
        $this->assertTrue(isset($array['email'], $array['name']));
    }

    public function trimProvider()
    {
        return [
            // array
            [['a', 'b'], ['a', ' b']],

            // string always trim
            [['a', 'b'], 'a, b'],

            // separator
            [['a', 'b'], 'a.b', '.'],
            [['a', 'b'], 'a/b', '/'],
        ];
    }

    /**
     * @dataProvider trimProvider
     */
    public function testTrim($expected, $array, $separator = ',')
    {
        $this->assertSame($expected, Arr::trim($array, $separator));
    }

    public function testTrimRecursive()
    {
        $this->assertSame(
            ['a', ['b', ['c']]],
            Arr::trimRecursive(['a ', ['b ', [' c ']]])
        );
        $this->assertSame(
            ['a', ['b.', ['c']]],
            Arr::trimRecursive(['a,', ['b.', ['/c/']]], '/,')
        );
    }

    public function testToArray()
    {
        $this->assertTrue(is_array(Arr::toArray([])));
        $this->assertTrue(is_array(
            Arr::toArray(['a' => new ArrayAccess()])['a']
        ));
        $this->assertTrue(is_array(
            Arr::toArray(new ArrayAccess())
        ));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/不支持的/');
        Arr::toArray('abcd');
    }

    public function testToAssocArray()
    {
        $data = Arr::toAssoc(['hello', 'world', 'someKey', 'someValue']);
        $this->assertEquals('world', $data['hello']);
        $this->assertEquals('someValue', $data['someKey']);
    }

    public function testRevertAssoc()
    {
        $data = Arr::revertAssoc([
            'hello' => 'world',
            'someKey' => 'someValue'
        ]);
        $this->assertEquals('hello', $data[0]);
        $this->assertEquals('world', $data[1]);
        $this->assertEquals('someKey', $data[2]);
        $this->assertEquals('someValue', $data[3]);
    }
}
<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\ArrayAccess;
use Wbaiyy\Base\Exceptions\UnknownPropertyException;

class ArrayAccessTest extends TestCase
{
    private $data = [
        'connections' => [
            'default' => [
                'host' => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname' => 'test',
                'port' => null,
            ],
        ],
        'nil' => null,
        'foo' => [
            'bar' => [
                'foobar' => true,
            ],
            'foobar' => true
        ]
    ];

    public function testConstruct()
    {
        $aa = new ArrayAccess($this->data);
        $this->assertSame('test', $aa->connections->default->dbname);

        $aa = new ArrayAccess(new ArrayAccess($this->data));
        $this->assertSame('test', $aa->connections['default']['dbname']);
        $this->assertNull($aa->connections['default']['notfound']);
    }

    public function testClone()
    {
        $origin = new ArrayAccess($this->data);
        $this->assertSame('', $origin->connections->default->password);
        $default = $origin->connections->default;
        $default->password = 'password';
        $this->assertEquals('password', $origin->connections->default->password);

        $clone = clone $origin;
        $clone->connections->default->password = 'new password';
        $this->assertEquals('new password', $clone->connections->default->password);
        $this->assertEquals('password', $origin->connections->default->password);
    }

    public function testMagicGet()
    {
        $aa = new ArrayAccess($this->data);
        $this->assertSame('test', $aa->connections->default->dbname);
        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessageRegExp('/notfound/');
        $aa->notfound;
    }

    public function testToArray()
    {
        $aa = new ArrayAccess($this->data);
        $this->assertSame($this->data, $aa->toArray());
    }

    public function testKeys()
    {
        $data = [
            0 => '',
            1 => 0,
            2 => '0',
        ];
        $aa = new ArrayAccess($data);
        $this->assertSame([0, 1, 2], $aa->keys());
        $this->assertSame([0, 1, 2], $aa->keys(0));
        $this->assertSame([1], $aa->keys(0, true));
    }

    public function testHasKey()
    {
        $data = [
            'key' => null,
        ];
        $aa = new ArrayAccess($data);
        $this->assertTrue($aa->hasKey('key'));
        $this->assertFalse($aa->hasKey('not-found'));
    }

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
        $aa = new ArrayAccess([]);
        $aa->merge($this->mergeA);
        $this->assertSame($aa->toArray(), $this->mergeA);

        // $argumentA -> ArrayAccess, preserveNumericKeys -> false
        $aa = new ArrayAccess($this->mergeA);
        $aa->merge($this->mergeB);
        $this->assertEquals('127.0.0.1', $aa->db->connections->default->host);
        $this->assertEquals('root', $aa->db->connections->default->password);
        $this->assertEquals('slave host', $aa->db->connections->slave->host);

        // numeric key
        $this->assertCount(3, $aa->db->connections->default->driverOptions);
        $this->assertSame('three', $aa[2][0]);

        // preserveNumericKeys -> true
        $aa = new ArrayAccess([]);
        $aa->merge(
            true,
            $this->mergeA,
            $this->mergeB,
            (new ArrayAccess(['more' => 'more']))->toArray()
        );
        $this->assertSame('more', $aa->more);

        // numeric key
        $this->assertCount(2, $aa->db->connections->default->driverOptions);
        $this->assertSame('three', $aa[1][0]);
        $this->assertNull($aa[2]);
    }

    public function testMagicIsset()
    {
        $aa = new ArrayAccess($this->data);
        $this->assertTrue(isset($aa->connections));
        $this->assertTrue(isset($aa->connections->default->host));
        $this->assertFalse(isset($aa->connections->default->nil));
        $this->assertFalse(isset($aa->nil));
        $this->assertFalse(isset($aa['nil']));
    }

    public function testMagicUnset()
    {
        $aa = new ArrayAccess($this->data);
        $this->assertFalse(isset($aa->nil));

        $aa['nil'] = true;
        $this->assertTrue(isset($aa->nil));
        unset($aa['nil']);
        $this->assertFalse(isset($aa->nil));
    }

    public function testCount()
    {
        $this->assertCount(count($this->data), new ArrayAccess($this->data));
    }

    public function testIterator()
    {
        $aa = new ArrayAccess($this->data);
        while ($aa->valid()) {
            if (null === $aa->current()) {
                break;
            }
            unset($aa->{$aa->key()});
            $aa->next();
        }

        $aa->next();
        $aa->rewind();
        // This test did not perform any assertions
        $this->assertTrue(true);
    }

    public function testSerialize()
    {
        $aa = new ArrayAccess($this->data);
        $serialize = serialize($aa);
        $unserialize = unserialize($serialize);
        $this->assertSame('', $unserialize->connections->default->password);
    }

    public function testJsonSerialize()
    {
        $aa = new ArrayAccess($this->data);
        $this->assertEquals(json_encode($aa), json_encode($this->data));
    }
}

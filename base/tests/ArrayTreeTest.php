<?php
namespace Wbaiyy\Tests;

use Wbaiyy\Base\ArrayTree;

class ArrayTreeTest extends TestCase
{
    private $data = [
        1 => [
            'id' => 1,
            'parent_id' => 0,
            'name' => 'item1',
        ],
        2 => [
            'id' => 2,
            'parent_id' => 0,
            'name' => 'item2',
        ],
        3 => [
            'id' => 3,
            'parent_id' => 0,
            'name' => 'item3',
        ],
        4 => [
            'id' => 4,
            'parent_id' => 2,
            'name' => 'item2-4',
        ],
        6 => [
            'id' => 6,
            'parent_id' => 5,
            'name' => 'item2-5-6',
        ],
        5 => [
            'id' => 5,
            'parent_id' => 2,
            'name' => 'item2-5',
        ],
    ];

    /**
     * @var ArrayTree
     */
    private $arrayTree;

    public function setUp()
    {
        parent::setUp();
        $this->arrayTree = new ArrayTree();
    }

    public function testArray2Tree()
    {
        $data = $this->arrayTree->array2tree($this->data);
        $this->assertCount(3, $data);
        $this->assertTrue(isset($data[0], $data[1], $data[2]));
        $this->assertFalse(isset($data[0]['children']));
        $this->assertTrue(isset($data[1]['children']));

        // preserveKeys -> true
        $data = $this->arrayTree->array2tree($this->data, true);
        $this->assertTrue(isset($data[1], $data[2], $data[3]));
        $this->assertSame(6, $data[2]['children'][5]['children'][6]['id']);
    }

    public function testTree2Array()
    {
        $data = $this->arrayTree->array2tree($this->data);
        $data = $this->arrayTree->tree2array($data);
        $this->assertCount(count($this->data), $data);
        $this->assertSame(1, $data[0]['id']);

        // idAsKey
        $data = $this->arrayTree->array2tree($this->data);
        $data = $this->arrayTree->tree2array($data, true);
        $this->assertSame(1, $data[1]['id']);
        $this->assertSame(3, $data[6]['treeInfo']['level']);

        // map
        $data = $this->arrayTree->array2tree($this->data);
        $data = $this->arrayTree->tree2array($data, true, 'id', function($key, $value, $level) {
            unset($key);
            $value['name'] = str_repeat('|', $level) . $value['name'];
            return $value;
        });
        $this->assertSame(3, substr_count($data[6]['name'], '|'));
    }

    public function testAddNode()
    {
        $data = require(__DIR__ . '/data/department.php');
        $this->assertTrue(isset($data[243]));
        $this->assertFalse(isset($data[1]['node']));
        $data = $this->arrayTree->addNode($data);
        $this->assertFalse(isset($data[243]));
        $this->assertTrue(isset($data[1]['node']));
    }
}
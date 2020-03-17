<?php
namespace ego\tests\modules\admin\models;

use app\modules\admin\models\DepartmentModel;
use app\modules\admin\models\MenuModel;
use ego\tests\models\ActiveRecordTest;
use ego\tests\TestCase;

class UnlimitedTraitTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        app()->redis->flushdb();
        app()->db->createCommand('TRUNCATE ' . DepartmentModel::tableName())->execute();
        app()->db->createCommand('TRUNCATE ' . MenuModel::tableName())->execute();
    }

    public function testGetFullName()
    {
        $department1 = ActiveRecordTest::insertDepartment('phpunit', 0, ['node' => 1]);
        $department2 = ActiveRecordTest::insertDepartment('phpunit2', 1, ['node' => '1,2']);
        $this->assertEquals(
            $department1->name,
            DepartmentModel::getFullName($department1->id)
        );

        $this->assertEquals(
            $department1->name . '>' . $department2->name,
            DepartmentModel::getFullName($department2)
        );
    }

    public function testGetSelectOptionsData()
    {
        ActiveRecordTest::insertDepartment('phpunit', 0, ['id' => 1]);
        ActiveRecordTest::insertDepartment('phpunit2', 1, ['id' => 2]);
        ActiveRecordTest::insertDepartment('phpunit3', 0, ['id' => 3]);

        $data = DepartmentModel::getSelectOptionsData();
        ActiveRecordTest::assertEquals('phpunit>phpunit2', $data[1]['name']);
        ActiveRecordTest::assertEquals('phpunit3', $data[2]['name']);
    }

    public function testUpdateNode()
    {
        // insert
        $this->assertEquals(1, MenuModel::updateNode([], ['id' => 1, 'parent_id' => 0]));

        // equals
        $this->assertTrue(MenuModel::updateNode(['parent_id' => 1], ['parent_id' => 1]));

        $this->insertMenu([
            'id' => 1,
            'name' => 'test1',
            'route' => 'test1',
            'parent_id' => 0,
            'node' => '1',
        ]);
        $menu2 = $this->insertMenu([
            'id' => 2,
            'name' => 'test2',
            'route' => 'test2',
            'parent_id' => 1,
            'node' => '1,2',
        ]);
        $this->insertMenu([
            'id' => 3,
            'name' => 'test3',
            'route' => 'test3',
            'parent_id' => 2,
            'node' => '1,2,3',
        ]);

        $this->assertEquals('1,2', MenuModel::getById(2, 'node'));
        $this->assertEquals('1,2,3', MenuModel::getById(3, 'node'));
        $this->assertEquals('2', MenuModel::updateNode($menu2->toArray(), ['parent_id' => 0]));
        $this->assertEquals('2', MenuModel::getById(2, 'node'));
        $this->assertEquals('2,3', MenuModel::getById(3, 'node'));
    }

    public function testUpdateNodeInternal()
    {
        $this->insertMenu([
            'id' => 1,
            'name' => 'test1',
            'route' => 'test1',
            'parent_id' => 0,
            'node' => '1',
        ]);
         $this->insertMenu([
            'id' => 2,
            'name' => 'test2',
            'route' => 'test2',
            'parent_id' => 1,
            'node' => '1,2',
        ]);
        $this->assertEquals(
            1,
            $this->invokeStaticMethod(MenuModel::class, 'updateNodeInternal', [1, 0])
        );
        $this->assertEquals(
            '1,2',
            $this->invokeStaticMethod(MenuModel::class, 'updateNodeInternal', [2, 1])
        );
    }

    protected function insertMenu($data)
    {
        MenuModel::getDb()->createCommand()->insert(MenuModel::tableName(), $data)->execute();
        return new MenuModel($data);
    }
}
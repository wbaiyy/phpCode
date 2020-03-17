<?php
namespace ego\tests\models;

use app\modules\admin\models\AdminModel;
use app\modules\admin\models\DepartmentModel;
use ego\tests\TestCase;
use ego\models\ActiveRecord;
use yii\db\ColumnSchema;

class ActiveRecordTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        app()->redis->flushdb();
        app()->db->createCommand()->truncateTable(AdminModel::tableName())->execute();
        app()->db->createCommand()->truncateTable(DepartmentModel::tableName())->execute();
    }

    public function testTableName()
    {
        $this->assertEquals('admin', Admin::tableName());
        $this->assertEquals('admin_action_history', AdminActionHistoryModel::tableName());
    }

    public function testGetById()
    {
        $this->assertNull(AdminModel::getById(1));
        $this->insertAdmin();
        $this->assertInstanceOf(AdminModel::class, AdminModel::getById(1));
        $this->assertEquals('phpunit', AdminModel::getById(1, 'username'));
    }

    public function testBeforeSave()
    {
        $model = new Admin();
        $this->assertNull($model->create_time);
        $this->assertNull($model->update_time);

        $model->beforeSave(true);
        $this->assertNotNull($model->create_time);
        $this->assertNull($model->update_time);

        $model = new Admin();
        $model->beforeSave(false);
        $this->assertNull($model->create_time);
        $this->assertNotNull($model->update_time);
    }

    public function testAfterSave()
    {
        $this->assertEquals('1', $this->insertDepartment('phpunit', 0, ['id' => 1])->node);
        $department = $this->insertDepartment('phpunit2', 1, ['id' => 2]);
        $this->assertEquals('1,2', $department->node);


        $department->update();
        $this->assertEquals('1,2', $department->node);

        $department->parent_id = 0;
        $department->update();
        $this->assertEquals('2', $department->node);
    }

    public function testAfterDelete()
    {
        $admin = $this->insertAdmin('foo');
        AdminModel::getById($admin->id);
        $this->assertEquals(
            1,
            app()->redis->hexists(AdminModel::getCacheKey(), $admin->id)
        );
        $admin->afterDelete();
        $this->assertEquals(
            0,
            app()->redis->hexists(AdminModel::getCacheKey(), $admin->id)
        );
    }

    public function testClearCache()
    {
        $admin = $this->insertAdmin();
        AdminModel::getByUsername($admin->username);

        $this->assertEquals(
            1,
            app()->redis->hexists(AdminModel::getCacheKey(), $admin->id)
        );
        $this->assertEquals(
            1,
            app()->redis->exists(AdminModel::getCacheKey('username2id'))
        );

        AdminModel::clearCache($admin);
        $this->assertEquals(
            0,
            app()->redis->hexists(AdminModel::getCacheKey(), $admin->id)
        );
        $this->assertEquals(
            0,
            app()->redis->exists(AdminModel::getCacheKey('username2id'))
        );

        $model2 = $this->insertAdmin('test2');
        AdminModel::getById($model2->id);
        $model3 = $this->insertAdmin('test3');
        AdminModel::getById($model3->id);

        $this->assertEquals(
            1,
            app()->redis->hexists(AdminModel::getCacheKey(), $model2->id)
        );
        $this->assertEquals(
            1,
            app()->redis->hexists(AdminModel::getCacheKey(), $model3->id)
        );
        AdminModel::clearCache([$model2->id, $model3->id]);
        $this->assertEquals(
            0,
            app()->redis->hexists(AdminModel::getCacheKey(), $model2->id)
        );
        $this->assertEquals(
            0,
            app()->redis->hexists(AdminModel::getCacheKey(), $model3->id)
        );
    }

    public function testGetCacheKey()
    {
        $this->assertSame(
            app()->cache->keyPrefix . 'admin',
            AdminModel::getCacheKey()
        );
        $this->assertSame(
            app()->cache->keyPrefix . 'admin:1',
            AdminModel::getCacheKey(1)
        );
        $this->assertSame(
            app()->cache->keyPrefix . 'department:1',
            DepartmentModel::getCacheKey(1)
        );
    }

    public function testFlattenErrors()
    {
        $model = new Admin();
        $model->addError('id', 'invalid id');
        $model->addError('id', 'id not found');
        $model->addError('username', 'user not found');

        $this->assertSame(
            ['invalid id', 'id not found', 'user not found'],
            $model->flattenErrors()
        );
        $this->assertSame(
            'invalid id, id not found, user not found',
            $model->flattenErrors(', ')
        );
        $this->assertSame(
            'invalid id, id not found',
            $model->flattenErrors(', ', 'id')
        );
    }

    public function testGetColumnInfo()
    {
        $model = new Admin();

        // attributeLabels
        $this->assertEquals('user name', $model->getColumnInfo('username', 'labelName'));

        $column = $model->getColumnInfo('id');
        // key -> null
        $this->assertInstanceOf(ColumnSchema::class, $column);
        $this->assertEquals('管理员id', $model->getColumnInfo('id', 'labelName'));
        $this->assertNotEquals('管理员id', $column->comment);

        $column = $model->getColumnInfo('department_id');
        $this->assertEquals('所属部门id', $model->getColumnInfo('department_id', 'labelName'));
        $this->assertEquals('所属部门id', $column->comment);

        $column = $model->getColumnInfo('email');
        $this->assertEquals('email', $model->getColumnInfo('email', 'labelName'));
        $this->assertEmpty($column->comment);

        $this->assertEquals('id', $model->getColumnInfo('id', 'name'));

        $this->assertNull($model->getColumnInfo('notfound'));
    }

    public function testGetAllLabelsByComment()
    {
        $model = new Admin();
        $this->assertEquals('管理员id', $model->getAllLabelsByComment()['id']);
    }

    public function testGetLabelByComment()
    {
        $model = new Admin();
        $this->assertEquals(
            '管理员id',
            $model->getLabelByComment($model->getColumnInfo('id'))
        );
    }

    public function testGetTableLabels()
    {
        $this->assertEquals('管理员', Admin::getTableLabels()['admin']);
        $this->assertEquals('no_label', Admin::getTableLabels()['no_label']);
    }

    public static function insertAdmin($username = 'phpunit', $data = [])
    {
        $model = new AdminModel();
        $model->username = $username;
        $model->setAttributes($data, false);
        $model->insert(false);
        return $model;
    }

    public static function insertDepartment($name = 'phpunit', $parentId = 0, array $data = [])
    {
        $model = new DepartmentModel();
        $model->name = $name;
        $model->parent_id = $parentId;
        $model->setAttributes($data, false);
        $model->insert(false);
        return $model;
    }
}

class Admin extends ActiveRecord
{
    public function attributeLabels()
    {
        return [
            'username' => 'user name'
        ];
    }
}

class AdminActionHistoryModel extends ActiveRecord
{

}

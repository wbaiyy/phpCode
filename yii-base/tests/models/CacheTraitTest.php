<?php
namespace ego\tests\models;

use app\modules\admin\models\AdminModel;
use ego\tests\TestCase;

class CacheTraitTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        app()->redis->flushdb();
        app()->db->createCommand()->truncateTable(AdminModel::tableName());
    }

    public function testGetById()
    {
        $this->assertNull(AdminModel::getById(1));

        $model = ActiveRecordTest::insertAdmin();
        $admin = AdminModel::getById($model->id);
        $this->assertEquals('phpunit', $admin->username);
        $this->assertEquals('phpunit', AdminModel::getById($model->id, 'username'));
    }
}
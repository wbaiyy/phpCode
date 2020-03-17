<?php
namespace ego\tests\base;

use ego\models\ValidatorModel;
use ego\tests\TestCase;

class ValidatorTest extends TestCase
{
    public function testNew()
    {
        $this->assertInstanceOf(ValidatorModel::class, app()->validatorModel->new([]));

        $val = app()->validatorModel->new([
            ['age', 'required'],
            [['id', 'age', 'is_enable'], 'number'],
        ]);
        $attributes = $val->attributes();
        $this->assertCount(3, $attributes);
        $this->assertTrue(isset($attributes['id'], $attributes['age'], $attributes['is_enable']));

        $val->load(['id' => '3', 'is_enable' => 'invalid'])->validate();
        $this->assertCount(2, $val->flattenErrors());
        $this->assertEmpty($val->flattenErrors(null, 'id'));
    }

    public function testSetAttributeLabels()
    {
        $attributeLabels = app()->validatorModel->setAttributeLabels([
            'id' => 'id',
            'name' => 'Name',
        ])->setAttributeLabels(['username' => 'username'])->attributeLabels();
        $this->assertEquals('id', $attributeLabels['id']);
        $this->assertEquals('Name', $attributeLabels['name']);
        $this->assertEquals('username', $attributeLabels['username']);
    }

    public function testLoad()
    {
        /** @var \stdClass $val */
        $val = app()->validatorModel->new([
            ['username', 'default', 'value' => ''],
            [['email'], 'default', 'value' => 'foo@example.com']
        ])->load(['username' => null]);
        $this->assertSame('', $val->username);
        $this->assertSame('foo@example.com', $val->email);
    }
}

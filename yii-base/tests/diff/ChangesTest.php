<?php
namespace ego\tests\diff;

use ego\diff\Changes;
use ego\models\ActiveRecord;
use ego\tests\TestCase;

class ChangesTest extends TestCase
{
    public function testGet()
    {
        $this->assertEmpty((new Changes())->get());

        $time = time();
        $old = [
            'username' => 'bar',
            'create_time' => $time - 3600,
            'password' => '567i8',
        ];
        $new = [
            'username' => 'foo',
            'create_time' => $time,
            'password' => '123456',
        ];
        $changes = new Changes(compact($old, $new));
        $this->assertArrayHasKey('create_time', $changes->get($old, $new));
    }

    public function testInit()
    {
        $changes = new Changes();
        $this->assertEmpty($changes->diffDetailFields);

        $changes->model = new AdminModel();
        $this->invokeMethod($changes, 'init');
        $this->assertTrue(in_array('text', $changes->diffDetailFields));
    }
}

class AdminModel extends ActiveRecord
{
}


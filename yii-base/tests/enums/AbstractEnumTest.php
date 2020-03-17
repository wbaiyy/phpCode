<?php
namespace ego\tests\enums;

use ego\tests\TestCase;
use ego\enums\AbstractEnum;

class AbstractEnumTest extends TestCase
{
    public function testGetAll()
    {
        $this->assertEmpty(AbstractEnum::getAll());
        $constants = GoodsError::getAll();
        $this->assertSame(GoodsError::ERR_SYSTEM_BUSY, $constants['ERR_SYSTEM_BUSY']);
        $this->assertCount(count($constants) - 1, GoodsError::getAll(true));
    }
}
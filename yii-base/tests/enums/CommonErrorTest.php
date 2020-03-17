<?php
namespace ego\tests\enums;

use ego\tests\TestCase;
use ego\enums\CommonError;

class CommonErrorTest extends TestCase
{
    public function testGetMessage()
    {
        $this->assertEquals(
            '系统繁忙，请稍后再试',
            CommonError::getMessage(CommonError::ERR_SYSTEM_BUSY)
        );
        $this->assertEquals(
            '系统繁忙，请稍后再试',
            UserError::getMessage('ERR' . time())
        );
        $this->assertEquals(
            'ERR_TEST',
            UserError::getMessage(UserError::ERR_TEST)
        );
    }

    public function testGetMessageByJavaCode()
    {
        $this->assertEquals(
            UserError::getMessageByJavaCode(-1),
            UserError::getMessage(UserError::ERR_EMAIL_INVALID)
        );
        $this->assertEquals(
            '系统繁忙，请稍后再试',
            UserError::getMessage(time())
        );
    }
}

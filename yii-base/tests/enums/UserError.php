<?php
namespace ego\tests\enums;

use ego\enums\CommonError;

class UserError extends CommonError
{
    const ERR_EMAIL_INVALID = 70000;
    const ERR_PASSWORD_EMPTY = 70001;
    const ERR_TEST = -70000;
    protected static $javaCode2phpCode = [
        -1 => self::ERR_EMAIL_INVALID
    ];


    /**
     * 根据java返回的错误码获取错误翻译信息
     *
     * @param int $code 错误码
     * @param array $params 翻译信息中占位符替换键值对数组
     * @param string $category 错误分类
     * @return string
     */
    public static function getMessageByJavaCode($code, $params = [], $category = null)
    {
        return static::getMessage(
            isset(static::$javaCode2phpCode[$code]) ? static::$javaCode2phpCode[$code] : static::ERR_SYSTEM_BUSY,
            $params,
            $category
        );
    }
}

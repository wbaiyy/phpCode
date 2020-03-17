<?php
namespace ego\tests;

use ego\phpunit\AbstractTestCase;
use app\base\Application;

/**
 * 测试基类
 */
class TestCase extends AbstractTestCase
{
   protected function createApp()
   {
       new Application($GLOBALS['CONFIG']);
   }
}

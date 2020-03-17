<?php
define('PHPUNIT_TEST', true);
define('YII_DEBUG', true);
define('YII_ENV', 'phpunit');
define('YII_ENV_TEST', true);
define('APP_PATH', __DIR__ . '/app');
define('VENDOR_PATH', __DIR__ . '/../vendor');

$CONFIG = require(__DIR__ . '/../src/bootstrap/bootstrap.php');

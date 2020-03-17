<?php

if (is_file($file = __DIR__ . '/.env.php')) {
    require $file;
}

// 这里的变量，都可在.env.php中用常量进行定义
// 网关url
defined('PHPUNIT_GATEWAY_URL') || define('PHPUNIT_GATEWAY_URL', 'http://10.4.4.203');
// app key
defined('PHPUNIT_GATEWAY_APP_KEY') || define('PHPUNIT_GATEWAY_APP_KEY', 'gateway');
// secret，默认取地址库aas的secret
defined('PHPUNIT_GATEWAY_SECRET') || define('PHPUNIT_GATEWAY_SECRET', '3e21ab62fb17400301d9f0156b6c3031');
// 后端url，不走网关，直接到后台测试返回code=0
if (!defined('PHPUNIT_GATEWAY_UPSTREAM_URL')) {
    define(
        'PHPUNIT_GATEWAY_UPSTREAM_URL',
        'http://www.php-gateway.com.mashanling.dev65.egocdn.com'
    );
}

require __DIR__.'/../vendor/autoload.php';

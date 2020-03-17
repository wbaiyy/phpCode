<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

if (is_file($file = __DIR__ . '/../config.php')) {
    $PREDIS_CONFIG = require($file);
} else {
    $PREDIS_CONFIG = [
        'parameters' => [
            [
                'host' => '192.168.6.176',
                'port' => 26390,
            ],
            [
                'host' => '192.168.6.176',
                'port' => 26391,
            ],
            [
                'host' => '192.168.6.176',
                'port' => 26392,
            ],
        ],
        'options' => [
            'replication' => 'sentinel',
            'service' => 'sentinel-192.168.6.176-26388',
        ]
    ];
}

<?php
return [
    'modules' => [
        'user' => 'app\modules\user\Module',
        'goods' => 'app\modules\goods\Module',
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.6.71;dbname=ego_yii_base_db',
            'username' => 'root',
            'password' => 'NvGHHsQvo3!90YS@',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
        ],
        'mailer' => [
            'messageConfig' => [
                'from' => [
                    'no_reply_system@newsletter.trendsgal.com' => 'Trendsgal.com'
                ],
            ],
            'mailTemplateModelClass' => 'ego\tests\mail\MailTemplateModel',
            'mailHistoryModelClass' => 'ego\tests\mail\MailHistoryModel',
        ],
        'phpProfile' => function() {
            return new class extends ego\base\PhpProfile
            {
                public function start()
                {
                    return false;
                }
            };
        }
    ]
];

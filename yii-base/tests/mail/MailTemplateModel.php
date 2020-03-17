<?php
namespace ego\tests\mail;

use ego\mail\MailTemplateModelInterface;

class MailTemplateModel implements MailTemplateModelInterface
{
    public static function getByUuqid($uuqid)
    {
        $data = [
            'subject' => '{$username}',
            'content' => '{$email}',
            'create_time' => time(),
            'update_time' => 0,
            'is_using' => 1,
        ];
        if ('phpunit_using_0' == $uuqid) {
            $data['is_using'] = 0;
        }
        if (false !== strpos($uuqid, 'phpunit')) {
            return $data;
        } else {
            return null;
        }
    }
}

<?php
namespace app\modules\admin\models;

use ego\models\CacheTrait;

/**
 * 管理员模型
 *
 * @property int $id
 * @property int $department_id
 * @property string $username
 * @property string $realname
 * @property string $email
 * @property int $role_id
 * @property int $is_leader
 * @property int $is_delete
 * @property int $is_enable
 * @property int $last_login_time
 * @property int $last_login_ip
 * @property int $logins
 */
class AdminModel extends ActiveRecord
{
    use CacheTrait;

    /**
     * 根据username获取管理员
     *
     * @param string $username
     * @return static|null
     */
    public static function getByUsername($username)
    {
        $key = static::getCacheKey('username2id');
        $id = app()->redis->hget($key, strtolower($username));
        // 命中缓存
        if ($id) {
            return static::getById($id);
        }

        /** @var static $item */
        $item = static::find()
            ->select('id,username')
            ->where(['username' => $username])
            ->one();
        if (!$item) {
            return null;
        }

        app()->redis->hset($key, strtolower($item->username), $item->id);
        return static::getById($item->id);
    }

    /**
     * @inheritdoc
     */
    public static function clearCache($id)
    {
        app()->redis->del(static::getCacheKey('username2id'));
        parent::clearCache($id);
    }
}
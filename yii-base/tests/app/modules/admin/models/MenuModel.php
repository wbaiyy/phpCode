<?php
namespace app\modules\admin\models;

use ego\models\UnlimitedTrait;

/**
 * 菜单模型
 *
 * @property string $route
 * @property string $front_page_route
 * @property int $is_show
 * @property int $is_leader_permission
 * @property string $memo
 */
class MenuModel extends ActiveRecord
{
    use UnlimitedTrait;
}
<?php
namespace app\modules\admin\models;

use ego\models\UnlimitedTrait;

/**
 * 部门模型
 *
 * @property string $memo
 */
class DepartmentModel extends ActiveRecord
{
    use UnlimitedTrait;
}
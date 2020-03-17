<?php
namespace app\modules\goods\traits;

use ego\base\ServiceLocator;

/**
 * 商品模块编辑器自动提示trait
 *
 * @property \app\modules\goods\components\GoodsComponent $GoodsComponent 商品组件
 */
trait MagicPropertyTrait
{
    public function __get($name)
    {
        if (ServiceLocator::isSupportedClassSuffix($name)) {
            return app()->Goods->{$name};
        } else {
            return parent::__get($name);
        }
    }
}

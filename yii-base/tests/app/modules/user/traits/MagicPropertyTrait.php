<?php
namespace app\modules\user\traits;

use ego\base\ServiceLocator;

trait MagicPropertyTrait
{
    public function __get($name)
    {
        if (ServiceLocator::isSupportedClassSuffix($name)) {
            return app()->User->{$name};
        } else {
            return parent::__get($name);
        }
    }
}

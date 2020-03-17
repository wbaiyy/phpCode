<?php
namespace ego\tests\enums;

use ego\enums\CommonError;

class GoodsError extends CommonError
{
    const ERR_PRODUCT_NOT_FOUND = 10000;
    const ERR_CATEGORY_NOT_FOUND = 10001;
    const ARRAY_CONSTANT = [];
}

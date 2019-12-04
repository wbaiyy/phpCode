<?php
//分表方法
function userOrderTable($userId)
{
    $countTable = 127;
    $userId = array_values(unpack("c*", md5($userId, true)));

    $number = 0;
    $userTable = ((($userId[3 + $number * 4] & 0xFF) << 24)
            | (($userId[2 + $number * 4] & 0xFF) << 16)
            | (($userId[1 + $number * 4] & 0xFF) << 8)
            | ($userId[$number * 4] & 0xFF))
            & 0xFFFFFFFF;

    $userTable= $userTable % 1000 & $countTable;
    return $userTable;
}
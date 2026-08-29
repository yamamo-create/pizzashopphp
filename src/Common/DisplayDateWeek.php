<?php

namespace App\Common;

use Datetime;

function displayDateWeek(string $registTime): string
{
    $t = new DateTime($registTime);
    $w = array('日', '月', '火', '水', '木', '金', '土');
    $week = $t->format('w');
    $dispTime = $t->format('Y-m-d') . '(' . $w[$week] . ')' . $t->format('G:i');

    return $dispTime;
}

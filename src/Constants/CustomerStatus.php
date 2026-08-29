<?php

namespace App\Constants;

require_once __DIR__ . '/../Config/Path.php';

class CustomerStatus
{
    public const POSSIBLE = 1;
    public const STOP = 9;

    // DB値 → 表示名
    public const LABELS = [
        self::POSSIBLE => '利用可能',
        self::STOP => 'アカウント停止中'
    ];

    // form文字列 → DB値
    public const FORM_VALUES = [
        'possible' => self::POSSIBLE,
        'stop'   => self::STOP
    ];
}

<?php

namespace App\Constants;

require_once __DIR__ . '/../Config/Path.php';

class AdminRole
{
    public const GENERAL = 0;
    public const ADMIN = 1;
    public const SUPER = 9;

    // DB値 → 表示名
    public const LABELS = [
        self::GENERAL => '一般',
        self::ADMIN => '管理者',
        self::SUPER => 'スーパー',
    ];

    // form文字列 → DB値
    public const FORM_VALUES = [
        'general' => self::GENERAL,
        'admin'   => self::ADMIN,
        'super'   => self::SUPER,
    ];
}

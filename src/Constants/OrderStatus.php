<?php

namespace App\Constants;

require_once __DIR__ . '/../Config/Path.php';

class OrderStatus
{
    public const DELETE = 0;
    public const RECEIVED = 1;
    public const WORKING = 2;
    public const SHIPPED = 3;
    public const COMPLETED = 4;
    public const CANCELED = 9;

    // DB値 → 表示名
    public const LABELS = [
        self::DELETE => '削除',
        self::RECEIVED => '受付',
        self::WORKING => '作業中',
        self::SHIPPED => '発送完了',
        self::COMPLETED => '完了',
        self::CANCELED => 'キャンセル'
    ];

    // form文字列 → DB値
    public const FORM_VALUES = [
        'deleted' => self::DELETE,
        'received'   => self::RECEIVED,
        'working'   => self::WORKING,
        'shipped'   => self::SHIPPED,
        'completed'   => self::COMPLETED,
        'canceled'   => self::CANCELED
    ];
}

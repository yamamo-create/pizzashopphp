<?php

namespace App\Common;

require_once __DIR__ . '/../Config/Path.php';
require_once SRC_PATH . '/Common/ErrLog.php';

use function App\Common\errLog;

use Throwable;

final class RedirectPage
{
    //管理
    public static function adminErrPage(Throwable|string $e = 'empty'): never
    {
        if ($e !== 'empty') {
            errLog($e);
        }
        header('Location: /admin/err/done.php');
        exit();
    }

    public static function adminTimeOutErrPage(Throwable|string $e = 'empty'): never
    {
        if ($e !== 'empty') {
            errLog($e);
        }
        header('Location: /admin/timeout/done.php');
        exit();
    }

    //顧客
    public static function customerErrPage(Throwable|string $e = 'empty'): never
    {
        if ($e !== 'empty') {
            errLog($e);
        }
        header('Location: /customer/err/done.php');
        exit();
    }
    public static function customerTimeOutErrPage(Throwable|string $e = 'empty'): never
    {
        if ($e !== 'empty') {
            errLog($e);
        }
        header('Location: /customer/timeout/done.php');
        exit();
    }
}

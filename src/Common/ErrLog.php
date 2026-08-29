<?php

namespace App\Common;

require_once __DIR__ . '/../Config/Path.php';

use Throwable;

function errLog(Throwable|string $e): void
{
    if ($e instanceof Throwable) {
        error_log(sprintf(
            "[%s] %s File:%s Line:%d",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));

        // ----- catch で throw した時のため ----
        // 例外チェーンをたどって原因となった例外をログに記録
        $previous = $e->getPrevious();

        while ($previous !== null) {

            error_log(sprintf(
                "Caused by: [%s] %s File:%s Line:%d",
                get_class($previous),
                $previous->getMessage(),
                $previous->getFile(),
                $previous->getLine()
            ));

            $previous = $previous->getPrevious();
        }
    } else {
        error_log($e);
    }
}

// use Throwable;

// final class ErrorLog
// {
//     private const ARRAY = '配列の値に問題があります';
//     private const ARGUMENT = '引数に問題があります';
//     private const POST = '$_POSTに問題があります';
//     private const SESSION = '$_SESSIONに問題があります';
//     private const SERVER = '$_SERVERに問題があります';
//     private const CSRF_TOKEN = 'CSRF tokenに問題があります';
//     private const CSRF_TOKEN_TIME_OUT = 'CSRF token が time out もしくは 直接URLを入力された可能性があります';
//     private const JSON_CSRF_TOKEN = 'JavaScriptと連携して使用している CSRF tokenに問題があります';
//     private const ONE_TIME_TOKEN = 'one time tokenに問題があります';
//     private const JSON_ONE_TIME_TOKEN = 'JavaScriptと連携して使用している one time tokenに問題があります';
//     private const TIME_OUT = 'time out もしくは 直接URLを入力された可能性があります';
//     private const CART = 'POST で送られた IDもしくは数量が不正です';
//     private const JSON_FILE = 'JSONファイルが読み込めません';
//     private const JSON_ENCODE_DECODE = 'JSONデータをエンコード、デコードできません';
//     private const ERROR = '不明なエラー';

//     public static function exception(Throwable $e): void
//     {
//         error_log(sprintf(
//             "[%s] %s\nFile:%s\nLine:%d",
//             get_class($e),
//             $e->getMessage(),
//             $e->getFile(),
//             $e->getLine()
//         ));
//     }

//     public static function arrayError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::ARRAY);
//     }
//     public static function argumentError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::ARGUMENT);
//     }
//     public static function postError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::POST);
//     }
//     public static function sessionError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::SESSION);
//     }
//     public static function serverError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::SERVER);
//     }
//     public static function csrfTokenError(string $file, int $line): void
//     {
//         $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
//         $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
//         $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
//         error_log($file . ':' . $line . ' ' . self::CSRF_TOKEN);
//         trigger_error("CSRFエラー METHOD:{$method} IP:{$ip} URI:{$uri}", E_USER_WARNING);
//     }
//     public static function csrfTokenTimeOutError(string $file, int $line): void
//     {
//         $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
//         $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
//         $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
//         error_log($file . ':' . $line . ' ' . self::CSRF_TOKEN_TIME_OUT);
//         trigger_error("CSRF time outエラー METHOD:{$method} IP:{$ip} URI:{$uri}", E_USER_WARNING);
//     }
//     public static function javaScriptCsrfTokenError(string $file, int $line): void
//     {
//         $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
//         $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
//         $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
//         error_log($file . ':' . $line . ' ' . self::JSON_CSRF_TOKEN);
//         trigger_error("JavaScript CSRFエラー METHOD:{$method} IP:{$ip} URI:{$uri}", E_USER_WARNING);

//         http_response_code(403);
//         header('Content-Type: application/json; charset=utf-8');
//         echo json_encode([
//             'status' => 'error',
//             'code' => 'CSRF_INVALID'
//         ]);
//     }
//     public static function oneTimeTokenError(string $file, int $line): void
//     {
//         $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
//         $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
//         $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
//         error_log($file . ':' . $line . ' ' . self::ONE_TIME_TOKEN);
//         trigger_error("OneTimeTokenエラー METHOD:{$method} IP:{$ip} URI:{$uri}", E_USER_WARNING);
//     }
//     public static function javaScriptOneTimeTokenError(string $file, int $line): void
//     {
//         $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
//         $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
//         $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
//         error_log($file . ':' . $line . ' ' . self::JSON_ONE_TIME_TOKEN);
//         trigger_error("JavaScript OneTimeTokenエラー METHOD:{$method} IP:{$ip} URI:{$uri}", E_USER_WARNING);

//         http_response_code(403);
//         header('Content-Type: application/json; charset=utf-8');
//         echo json_encode([
//             'status' => 'error',
//             'code' => 'ONE_TIME_TOKEN_INVALID'
//         ]);
//     }
//     public static function itmeOutError(string $file, int $line): void
//     {
//         $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
//         $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
//         $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
//         error_log($file . ':' . $line . ' ' . self::TIME_OUT);
//         trigger_error("time outエラー METHOD:{$method} IP:{$ip} URI:{$uri}", E_USER_WARNING);
//     }
//     public static function cartError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::CART);
//         trigger_error("cartの不正検出 IP:{$_SERVER['REMOTE_ADDR']} URI:{$_SERVER['REQUEST_URI']}", E_USER_NOTICE);
//         header('Content-Type: application/json');
//         echo json_encode(['status' => 'error']);
//     }
//     public static function jsonFileError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::JSON_FILE);
//     }
//     public static function jsonEncodeDecodeError(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::JSON_ENCODE_DECODE);
//     }
//     public static function error(string $file, int $line): void
//     {
//         error_log($file . ':' . $line . ' ' . self::ERROR);
//     }
// }

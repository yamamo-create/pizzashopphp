<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/ErrLog.php';
require_once SRC_PATH . '/Common/JsonResponse.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Service/CustomerTop.php';

use function App\Common\errLog;
use App\Common\JsonResponse;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Service\CustomerTop;

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errLog(__FILE__ . 'line:' . __LINE__ . ' REQUEST_METHOD: NOT POST');
    JsonResponse::error(403, 'NOT POST');
}

if (CustomerCsrf::isTimeout()) {
    errLog(__FILE__ . 'line:' . __LINE__ . ' timeout OR direct URL');
    JsonResponse::error(403, 'CSRF_TIME_OUT');
}

if (!CustomerCsrf::validate($_POST['customer_csrf_token'] ?? null)) {
    errLog(__FILE__ . 'line:' . __LINE__ . ' CSRF illegal');
    JsonResponse::error(403, 'CSRF_INVALID');
}

$customerTop = new CustomerTop();

//catchは上から、データ不整合、システム異常、それ以外
try {
    $customerTop->addProduct($_POST);
} catch (InvalidArgumentException $e) {
    errLog($e);
    JsonResponse::error(400, 'INVALID_ERROR');
} catch (RuntimeException $e) {
    errLog($e);
    JsonResponse::error(500, 'SYSTEM_ERROR');
} catch (Throwable $e) {
    errLog($e);
    JsonResponse::error(500, 'UNKNOWN_ERROR');
}

JsonResponse::success();

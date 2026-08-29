<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';
require_once SRC_PATH . '/Customer/Service/CustomerTop.php';

use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Token\CustomerOneTimeToken;
use App\Customer\Service\CustomerTop;

session_start();

$sessionValidator = new CustomerSessionValidator();
$customerTop = new CustomerTop();

// ----- ログイン維持用 -----
$is_login = $_SESSION['customer']['login']['is_login'] ?? null;
$login_id = $_SESSION['customer']['login']['id'] ?? null;
$login_email = $_SESSION['customer']['login']['email'] ?? null;
try {
    $sessionValidator->validateIsLogin($is_login);
    $sessionValidator->validateLoginIdEmail($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::customerTimeOutErrPage($e);
}
$loginMessage = "（{$login_id}）{$login_email}さんログイン中";
// ----------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' REQUEST_METHOD: NOT POST'
    );
}

if (!CustomerCsrf::validate($_POST['customer_csrf_token'] ?? null)) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' CSRF TOKEN doubt'
    );
}

if (!CustomerOneTimeToken::validateAndDestroy('delivery_onetime', $_POST['delivery_onetime'] ?? null)) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' OneTimeToken doubt'
    );
}

$totalPrice = $_SESSION['customer']['order']['total_price'] ?? null;
$cart = $_SESSION['customer']['cart'] ?? null;

try {
    $sessionValidator->validateTotalPrice($totalPrice);
    $sessionValidator->validateCartIdQuantity($cart);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

try {
    $orderInfo = $customerTop->getOrderInfo($login_id, $totalPrice);
    $orderitemInfo = $customerTop->getOrderitemInfo($cart);
    $orderId = $customerTop->createOrder($orderInfo, $orderitemInfo);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

unset($_SESSION['customer']['order']);
unset($_SESSION['customer']['cart']);

$_SESSION['customer']['order']['order_id'] = $orderId;
$_SESSION['flash']['success'] = 'ORDER_SUCCESS';

header('Location: complete.php');
exit();

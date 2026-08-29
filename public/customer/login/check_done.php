<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Service/CustomerLogin.php';

use App\Common\RedirectPage;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Service\CustomerLogin;

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' REQUEST_METHOD: NOT POST'
    );
}

if (CustomerCsrf::isTimeout()) {
    RedirectPage::customerTimeOutErrPage(
        __FILE__ . 'line:' . __LINE__ . ' timeout OR direct URL'
    );
}

if (!CustomerCsrf::validate($_POST['customer_csrf_token'] ?? null)) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' CSRF TOKEN doubt'
    );
}

$customerLogin = new CustomerLogin();

$customerLogin->validatePost($_POST);

if (
    $customerLogin->geterrorFlag() === true ||
    !empty($customerLogin->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $customerLogin->getErrorMessage();
    header('Location: enter.php');
    exit();
}

$customerLogin->setAccount($_POST['email'] ?? '', $_POST['password'] ?? '');

try {
    $customerLogin->canLoginIp();
    $customerLogin->canLoginEmail();

    $customerLogin->verifyPassword();
    $customerLogin->verifyStatus();
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

if (
    $customerLogin->getLoginFlag() === false ||
    $customerLogin->geterrorFlag() === true
) {
    // ----- ログイン失敗 -----
    try {
        $customerLogin->failIp();
        $customerLogin->failEmail();
    } catch (Throwable $e) {
        RedirectPage::customerErrPage($e);
    }
    $_SESSION['flash']['errors'] = $customerLogin->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- ログイン成功 -----
try {
    $customerLogin->successIp();
    $customerLogin->successEmail();
    $customerLogin->successLogin();
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

header('Location: /customer/top/index.php');
exit();

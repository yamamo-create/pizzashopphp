<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerView.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';

use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Service\CustomerView;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Token\CustomerOneTimeToken;

session_start();

$sessionValidator = new CustomerSessionValidator();
$customerView = new CustomerView();

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

if (!CustomerOneTimeToken::validateAndDestroy('withdraw_onetime', $_POST['withdraw_onetime'] ?? null)) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' OneTimeToken doubt'
    );
}

$customerView->validatePostWithdraw($_POST);

if (
    $customerView->getErrorFlag() === true ||
    !empty($customerView->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $customerView->getErrorMessage();
    header('Location: enter.php');
    exit();
}

$password = $_POST['password'] ?? null;

try {
    $customerView->verifyPassword($login_email, $password);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

if ($customerView->getErrorFlag() === true) {
    $_SESSION['flash']['errors'] = $customerView->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- 削除 -----

try {
    $customerView->deleteAccount($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

$_SESSION['flash']['success'] = 'CUSTOMER_WITHDRAW_SUCCESS';

header('Location: complete.php');
exit();

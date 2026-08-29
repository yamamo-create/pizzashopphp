<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerView.php';
require_once SRC_PATH . '/Customer/Service/CustomerLogin.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';

use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Service\CustomerView;
use App\Customer\Service\CustomerLogin;
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

if (!CustomerOneTimeToken::validateAndDestroy('customer_change_email_onetime', $_POST['customer_change_email_onetime'] ?? null)) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' OneTimeToken doubt'
    );
}

$customerData = $customerView->trimCustomerChangeEmail($_POST);
$customerView->validatePostChangeEmail($customerData);

if (
    $customerView->getErrorFlag() === true ||
    !empty($customerView->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $customerView->getErrorMessage();
    header('Location: enter.php');
    exit();
}

$newEmail = $customerData['email'] ?? null;
$password = $customerData['password'] ?? null;

try {
    $customerView->validateChangeEmail($newEmail, $login_email, $password);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

if (
    $customerView->getErrorFlag() === true ||
    !empty($customerView->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $customerView->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- メールアドレス変更 -----
try {
    $customerView->changeCustomerEmail($login_id, $login_email, $newEmail);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

if (
    $customerView->getErrorFlag() === true ||
    !empty($customerView->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $customerView->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- 変更したメールアドレスでログイン -----

$customerView->cleaningCustomerData();

$customerLogin = new CustomerLogin();

$customerLogin->setAccount($newEmail, $password);

try {
    $customerLogin->verifyPassword();
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

if (
    $customerLogin->geterrorFlag() === true ||
    $customerLogin->getLoginFlag() === false
) {
    $_SESSION['flash']['errors']['system'] =
        '予期せぬエラーが発生しました。登録に使ったメールアドレスで、このページのお問合せにメールしてください';
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

unset($_SESSION['customer']['form']['change_email']);
$_SESSION['flash']['success'] = 'CUSTOMER_CHANGE_EMAIL_SUCCESS';

header('Location: complete.php');
exit();

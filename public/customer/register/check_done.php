<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Service/CustomerRegister.php';
require_once SRC_PATH . '/Customer/Service/CustomerLogin.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';

use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Service\CustomerRegister;
use App\Customer\Service\CustomerLogin;
use App\Customer\Token\CustomerOneTimeToken;

session_start();

$sessionValidator = new CustomerSessionValidator();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' REQUEST_METHOD: NOT POST'
    );
}

if (CustomerCsrf::isTimeout()) {
    RedirectPage::customerTimeOutErrPage(
        __FILE__ . 'line:' . __LINE__ . ' Timeout OR Direct URL'
    );
}

if (!CustomerCsrf::validate($_POST['customer_csrf_token'] ?? null)) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' CSRF TOKEN doubt'
    );
}

if (!CustomerOneTimeToken::validateAndDestroy('register_onetime', $_POST['register_onetime'] ?? null)) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' OneTimeToken doubt'
    );
}

$customerRegister = new CustomerRegister();

$customerData = $customerRegister->trimCustomerData($_POST);
$customerRegister->validatePost($customerData);

$errorFlag = $customerRegister->getErrorFlag();
$errorMessage = $customerRegister->getErrorMessage();

foreach ($customerData as $key => $value) {
    if (
        $key !== 'password1' &&
        $key !== 'password2' &&
        empty($errorMessage[$key])
    ) {
        $_SESSION['customer']['form']['register'][$key] = $customerData[$key];
    }
}

if ($errorFlag === true || !empty($errorMessage)) {
    $_SESSION['flash']['errors'] = $customerRegister->getErrorMessage();
    header('Location: enter.php');
    exit();
}

try {
    $customerRegister->isEmailAvailable($customerData['email']);
    $customerRegister->isPassword1and2Same($customerData['password1'], $customerData['password2']);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

if (
    $customerRegister->getErrorFlag() === true ||
    !empty($customerRegister->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $customerRegister->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- ここから登録 ------

$customerData['password'] = $customerData['password1'];
unset($customerData['password1']);
unset($customerData['password2']);

try {
    $customerRegister->createCustomerAccount($customerData);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

// ------ 入力した登録情報でログイン -----
$customerRegister->cleaningSession();

$customerLogin = new CustomerLogin();

$customerLogin->setAccount($customerData['email'], $customerData['password']);

try {
    $customerLogin->verifyPassword();
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

if (
    $customerLogin->getLoginFlag() === false ||
    $customerLogin->geterrorFlag() === true
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

$_SESSION['flash']['success'] = 'CUSTOMER_REGISTER_SUCCESS';

header('Location: complete.php');
exit();

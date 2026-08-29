<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Service/AdminLogin.php';

use App\Common\RedirectPage;
use App\Admin\Token\AdminCsrf;
use App\Admin\Service\AdminLogin;

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . ' REQUEST_METHOD: NOT POST'
    );
}

if (AdminCsrf::isTimeout()) {
    RedirectPage::adminTimeOutErrPage(
        __FILE__ . 'line:' . __LINE__ . ' timeout OR direct URL'
    );
}

if (!AdminCsrf::validate($_POST['admin_csrf_token'] ?? null)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . ' CSRF TOKEN doubt'
    );
}

$adminLogin = new AdminLogin();

$adminLogin->validatePost($_POST);

if (
    $adminLogin->geterrorFlag() === true ||
    !empty($adminLogin->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminLogin->getErrorMessage();
    header('Location: enter.php');
    exit();
}

$adminLogin->setAccount($_POST['email'] ?? '', $_POST['password'] ?? '');

try {
    $adminLogin->canLoginIp();
    $adminLogin->canLoginEmail();
    $adminLogin->verifyPassword();
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

if (
    $adminLogin->getLoginFlag() === false &&
    $adminLogin->geterrorFlag() === true
) {
    try {
        $adminLogin->failIp();
        $adminLogin->failEmail();
    } catch (Throwable $e) {
        RedirectPage::adminErrPage($e);
    }
    $_SESSION['flash']['errors'] = $adminLogin->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- ログイン成功 -----
try {
    $adminLogin->successIp();
    $adminLogin->successEmail();
    $adminLogin->successLogin();
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

header('Location: /admin/index.php');
exit();

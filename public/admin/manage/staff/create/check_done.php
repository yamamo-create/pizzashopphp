<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManageStaff.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManageStaff;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminManageStaff = new AdminManageStaff();

// ----- ログイン維持用 -----
$is_login = $_SESSION['admin']['login']['is_login'] ?? null;
$login_id = $_SESSION['admin']['login']['id'] ?? null;
$login_email = $_SESSION['admin']['login']['email'] ?? null;
try {
    $sessionValidator->validateIsLogin($is_login);
    $sessionValidator->validateLoginIdEmail($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::adminTimeOutErrPage($e);
}
$loginMessage = "（{$login_id}）{$login_email}さんログイン中";
// ----------

// ----- 管理者権限 -----
if (
    $_SESSION['admin']['login']['auth'] !== AdminRole::ADMIN &&
    $_SESSION['admin']['login']['auth'] !== AdminRole::SUPER
) {
    RedirectPage::adminErrPage(__FILE__ . __LINE__ . 'Not Admin Super');
}
// ----------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . ' REQUEST_METHOD: NOT POST'
    );
}

if (!AdminCsrf::validate($_POST['admin_csrf_token'] ?? null)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . ' CSRF TOKEN doubt'
    );
}

if (!AdminOneTimeToken::validateAndDestroy(
    'admin_staff_create_one_token',
    $_POST['admin_staff_create_one_token'] ?? null
)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . '  OneTimeToken doubt'
    );
}

$staffData = $adminManageStaff->trimStaffData($_POST);
$adminManageStaff->validatePostCreate($staffData);

if (
    $adminManageStaff->getErrorFlag() === true ||
    !empty($adminManageStaff->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManageStaff->getErrorMessage();
    header('Location: enter.php');
    exit();
}

try {
    $adminManageStaff->isEmailAvailable($staffData['email']);
    $adminManageStaff->isPassword1and2Same($staffData['password1'], $staffData['password2']);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

if (
    $adminManageStaff->getErrorFlag() === true ||
    !empty($adminManageStaff->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManageStaff->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- ここから登録 ------

$staffData['password'] = $staffData['password1'];
unset($staffData['password1']);
unset($staffData['password2']);

try {
    $adminManageStaff->createAdminAccount($staffData);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

unset($_SESSION['admin']['form']['create']);

header('Location: /admin/manage/staff/list.php');
exit();

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
    'admin_staff_update_one_token',
    $_POST['admin_staff_update_one_token'] ?? null
)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . '  OneTimeToken doubt'
    );
}

$adminManageStaff->validatePostUpdate($_POST);

if (
    $adminManageStaff->getErrorFlag() === true ||
    !empty($adminManageStaff->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManageStaff->getErrorMessage();
    header('Location: enter.php');
    exit();
}

$staffPassword = $_POST['staff_password'] ?? null;
$newPassword1 = $_POST['new_password1'] ?? null;
$newPassword2 = $_POST['new_password2'] ?? null;

try {
    $adminManageStaff->verifyStaffPassword($login_email, $staffPassword);
    $adminManageStaff->isSuperStaffPassword1and2Same($newPassword1, $newPassword2);
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

// ----- スーパーアカウントのパスワード変更 -----
try {
    $adminManageStaff->updateSuperStaffPassword($newPassword1);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$_SESSION['flash']['success'] = 'ADMINSUPER_PASSWROD_CHANGE_SUCCESS';

header('Location: complete.php');
exit();

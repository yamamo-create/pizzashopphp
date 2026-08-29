<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Constants/CustomerStatus.php';
require_once SRC_PATH . '/Admin/Service/AdminManageCustomer.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Constants\CustomerStatus;
use App\Admin\Service\AdminManageCustomer;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminManageCustomer = new AdminManageCustomer();

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
    'customerlist_one_token',
    $_POST['customerlist_one_token'] ?? null
)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . '  OneTimeToken doubt'
    );
}

try {
    $adminManageCustomer->validatePostDone($_POST);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$choice = $_POST['choice'] ?? '';
$customerId = $_SESSION['admin']['form']['customer'] ?? null;

try {
    $sessionValidator->validateCustomerChoiceId($customerId);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$status = CustomerStatus::FORM_VALUES[$choice] ?? null;

try {
    $adminManageCustomer->changeStatus($customerId, $status);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

header('Location: confirm.php');
exit();

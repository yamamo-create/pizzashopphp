<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManageStaff.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManageStaff;
use App\Admin\Token\AdminCsrf;

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

//choice「create、read、update、delete」の４種類
try {
    $adminManageStaff->validatePostBranchChoice($_POST);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$choice = $_POST['choice'] ?? '';

if ($choice === 'create') {
    header('Location: /admin/manage/staff/create/enter.php');
    exit();
}

if ($choice === 'update') {
    header('Location: /admin/manage/staff/update/enter.php');
    exit();
}

if (empty($_POST['choice_id'] ?? '')) {
    $_SESSION['flash']['errors']['choice'] = 'スタッフを選択してください';
    header('Location: list.php');
    exit();
}

try {
    $adminManageStaff->validatePostBranchChoiceId($_POST);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$choiceId = $_POST['choice_id'] ?? '';

switch ($choice) {
    case 'read':
        $_SESSION['admin']['system']['choice_id'] = $choiceId;
        header('Location: /admin/manage/staff/read/enter.php');
        exit();
        break;

    case 'delete':
        $_SESSION['admin']['system']['choice_id'] = $choiceId;
        header('Location: /admin/manage/staff/delete/enter.php');
        exit();
        break;

    default:
        RedirectPage::adminErrPage();
        break;
}
RedirectPage::adminErrPage();

<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManagePagecreate.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManagePagecreate;
use App\Admin\Token\AdminCsrf;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminManagePagecreate = new AdminManagePagecreate();

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

try {
    $adminManagePagecreate->validatePost($_POST);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$meal = $_POST['meal'] ?? '';
$dessert = $_POST['dessert'] ?? '';

try {
    $mealParsData = $adminManagePagecreate->parsePagecreateData($meal);
    $dessertParsData = $adminManagePagecreate->parsePagecreateData($dessert);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$adminManagePagecreate->validateParseData($mealParsData, $dessertParsData);

if (
    $adminManagePagecreate->getErrorFlag() === true ||
    !empty($adminManagePagecreate->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManagePagecreate->getErrorMessage();
    header('Location: enter.php');
    exit();
}

$_SESSION['admin']['form']['page']['meal'] = $mealParsData;
$_SESSION['admin']['form']['page']['dessert'] = $dessertParsData;

header('Location: confirm.php');
exit();

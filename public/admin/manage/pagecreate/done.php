<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManagePagecreate.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManagePagecreate;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

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

if (!AdminOneTimeToken::validateAndDestroy(
    'admin_pagecreate_one_token',
    $_POST['admin_pagecreate_one_token'] ?? null
)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . '  OneTimeToken doubt'
    );
}

$meal = $_SESSION['admin']['form']['page']['meal'] ?? null;
$dessert = $_SESSION['admin']['form']['page']['dessert'] ?? null;

try {
    $sessionValidator->validatePagecreatePuroductId($meal);
    $sessionValidator->validatePagecreatePuroductId($dessert);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$adminManagePagecreate->validateParseData($meal, $dessert);

if (
    $adminManagePagecreate->getErrorFlag() === true ||
    !empty($adminManagePagecreate->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManagePagecreate->getErrorMessage();
    header('Location: enter.php');
    exit();
}

try {
    $mealData = $adminManagePagecreate->getProductDatas($meal);
    $dessertData = $adminManagePagecreate->getProductDatas($dessert);
    $mealJson = $adminManagePagecreate->encodeJson($mealData);
    $dessertJson = $adminManagePagecreate->encodeJson($dessertData);
    $adminManagePagecreate->putJsonData($mealJson, 'meal.json');
    $adminManagePagecreate->putJsonData($dessertJson, 'dessert.json');
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

if (
    $adminManagePagecreate->getErrorFlag() === true ||
    !empty($adminManagePagecreate->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManagePagecreate->getErrorMessage();
    header('Location: confirm.php');
    exit();
}

unset($_SESSION['admin']['form']['page']);

$_SESSION['flash']['success'] = 'ADMIN_PAGE_SUCCESS';

header('Location: complete.php');
exit();

<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManageProduct.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManageProduct;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminManageProduct = new AdminManageProduct();

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
    'admin_product_create_one_token',
    $_POST['admin_product_create_one_token'] ?? null
)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . '  OneTimeToken doubt'
    );
}

$productData = [];

$productData = $adminManageProduct->trimPostCreate($_POST);
$adminManageProduct->validatePostCreate($productData, $_FILES);

$errorFlag = $adminManageProduct->getErrorFlag();
$errorMessage = $adminManageProduct->getErrorMessage();

foreach ($productData as $key => $value) {
    if (
        $key !== 'password1' &&
        $key !== 'password2' &&
        empty($errorMessage[$key])
    ) {
        $_SESSION['admin']['form']['product_create'][$key] = $productData[$key];
    }
}

if ($errorFlag === true || !empty($errorMessage)) {
    $_SESSION['flash']['errors'] = $adminManageProduct->getErrorMessage();
    header('Location: enter.php');
    exit();
}

try {
    $adminManageProduct->validateImageFileCreate($_FILES);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

if (
    $adminManageProduct->getErrorFlag() === true ||
    !empty($adminManageProduct->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManageProduct->getErrorMessage();
    header('Location: enter.php');
    exit();
}

// ----- ここから登録 -----

try {
    $adminManageProduct->createProduct($productData, $_FILES);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

unset($_SESSION['admin']['form']['product_create']);

header('Location: /admin/manage/product/list.php');
exit();

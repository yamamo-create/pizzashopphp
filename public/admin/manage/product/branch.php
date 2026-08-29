<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManageProduct.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManageProduct;
use App\Admin\Token\AdminCsrf;

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

//choiceは、「create、read、update、delete」の４種類のみ。それ以外は、改竄されているのでログアウト
try {
    $adminManageProduct->validatePostBranchChoice($_POST);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$choice = $_POST['choice'] ?? '';

if ($choice === 'create') {
    header('Location: /admin/manage/product/create/enter.php');
    exit();
}

if (empty($_POST['choice_id'] ?? '')) {
    $_SESSION['flash']['errors']['choice'] = '商品を選択してください';
    header('Location: list.php');
    exit();
}

try {
    $adminManageProduct->validatePostBranchChoiceId($_POST);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$choiceId = $_POST['choice_id'] ?? '';

if ($choice === 'read') {
    $_SESSION['admin']['system']['choice_id'] = $choiceId;
    header('Location: /admin/manage/product/read/enter.php');
    exit();
}

try {
    $mealJson = $adminManageProduct->getJsonData('meal.json');
    $dessertJson = $adminManageProduct->getJsonData('dessert.json');
    $meal = $adminManageProduct->decodeJson($mealJson);
    $dessert = $adminManageProduct->decodeJson($dessertJson);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$UseProductIds = $adminManageProduct->getUseProductIds($meal, $dessert);
$adminManageProduct->validateProductNotUsedBranch($UseProductIds, $choiceId);

if (
    $adminManageProduct->getErrorFlag() === true ||
    !empty($adminManageProduct->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManageProduct->getErrorMessage();
    header('Location: list.php');
    exit();
}

if ($choice === 'update') {
    $_SESSION['admin']['system']['choice_id'] = $choiceId;
    header('Location: /admin/manage/product/update/enter.php');
    exit();
}

if ($choice === 'delete') {
    $_SESSION['admin']['system']['choice_id'] = $choiceId;
    header('Location: /admin/manage/product/delete/enter.php');
    exit();
}

RedirectPage::adminErrPage();

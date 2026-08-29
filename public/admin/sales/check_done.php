<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/OrderStatus.php';
require_once SRC_PATH . '/Admin/Service/AdminSales.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\OrderStatus;
use App\Admin\Service\AdminSales;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminSales = new AdminSales();

// ----- ログイン維持用 -----
$is_login = $_SESSION['admin']['login']['is_login'] ?? '';
$login_id = $_SESSION['admin']['login']['id'] ?? '';
$login_email = $_SESSION['admin']['login']['email'] ?? '';
try {
    $sessionValidator->validateIsLogin($is_login);
    $sessionValidator->validateLoginIdEmail($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::adminTimeOutErrPage($e);
}
$loginMessage = "（{$login_id}）{$login_email}さんログイン中";
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
    'admin_sales_one_token',
    $_POST['admin_sales_one_token'] ?? null
)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . '  OneTimeToken doubt'
    );
}

if (empty($_POST['choice_id'] ?? '')) {
    $_SESSION['flash']['errors']['choice'] = '注文番号を選択してください';
    header('Location: enter.php');
    exit();
}

try {
    $adminSales->validatePost($_POST);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$choiceId = $_POST['choice_id'] ?? '';
$choice = $_POST['choice'] ?? '';

$orderStatus = OrderStatus::FORM_VALUES[$choice];

try {
    $adminSales->updateStatus($choiceId, $orderStatus);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

header('Location: enter.php');
exit();

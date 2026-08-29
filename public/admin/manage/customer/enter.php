<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Constants/CustomerStatus.php';
require_once SRC_PATH . '/Admin/Service/AdminManageCustomer.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';

use function App\Common\h;
use function App\Common\displayDateWeek;
use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Constants\CustomerStatus;
use App\Admin\Service\AdminManageCustomer;
use App\Admin\Token\AdminCsrf;

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

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

$customerData = [];

try {
    $customerData = $adminManageCustomer->getCustomerAllData();
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$admin_csrf_token = AdminCsrf::ensure();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>元気pizza</title>
    <link rel="stylesheet" href="/initialsetting.css" />
    <link rel="stylesheet" href="/header_footer.css" />
    <link rel="stylesheet" href="/style.css" />
</head>

<body>
    <div class="body-wrapper">

        <?php require_once PUBLIC_PATH . '/admin_header.php'; ?>

        <main>
            <div class="main-wrapper">
                <section>
                    <h2>顧客一覧</h2>
                </section>

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $value): ?>
                        <section class="color-red"><?php echo h($value); ?></section>
                    <?php endforeach ?>
                <?php endif ?>

                <section>
                    <?php echo h($loginMessage) ?>
                </section>

                <section>
                    <a class="button" href="cancel.php">戻る</a>
                </section>

                <section class="bgcolor">
                    <article>
                        <form method="post" action="check.php">
                            <?php foreach ($customerData as $data): ?>
                                <?php
                                $id = $data['id'] ?? '';
                                $status = empty($data['status']) ? '' : CustomerStatus::LABELS[$data['status']] ?? '';
                                $name = $data['name'] ?? '';
                                $email = $data['email'] ?? '';
                                $time = empty($data['created_at']) ? '' : displayDateWeek($data['created_at']);
                                ?>
                                <p>
                                    <?php echo h($status) . '（' . h($id) . '）' .  h($name) . '　' . h($email) . '　' . h($time); ?>
                                    <button class="button" type="submit" name="choice_id" value="<?php echo h($id); ?>">詳細</button>
                                </p>
                                <br><br>
                            <?php endforeach ?>
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                        </form>
                    </article>
                </section>

                <section>
                    <a class="button" class="button" href="cancel.php">戻る</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Admin/Service/AdminManageStaff.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use function App\Common\h;
use function App\Common\displayDateWeek;
use App\Common\RedirectPage;
use App\Constants\AdminRole;
use App\Admin\Validator\AdminSessionValidator;
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

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

$choiceId = $_SESSION['admin']['system']['choice_id'] ?? null;
unset($_SESSION['admin']['system']['choice_id']);

try {
    $sessionValidator->validateSystemChoiceId($choiceId);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

try {
    $staffData = $adminManageStaff->getAdminStaffData($choiceId);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$id = $staffData['id'] ?? '';
$auth = $staffData['auth'] ?? '';
$email = $staffData['email'] ?? '';
$created_at = $staffData['created_at'] ?? '';

$adminManageStaff->checkDeleteYourself($login_email, $email);

if (
    $adminManageStaff->getErrorFlag() === true ||
    !empty($adminManageStaff->getErrorMessage())
) {
    $_SESSION['flash']['errors'] = $adminManageStaff->getErrorMessage();
    header('Location: cancel.php');
    exit();
}

if ($auth === 9) {
    header('Location: cancel.php');
    exit();
}

$auth = AdminRole::LABELS[$auth];

$_SESSION['admin']['form']['delete']['id'] = $id;
$_SESSION['admin']['form']['delete']['email'] = $email;

$admin_csrf_token = AdminCsrf::ensure();
$admin_staff_delete_one_token = AdminOneTimeToken::generate('admin_staff_delete_one_token');
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
                    <h2>スタッフ削除</h2>
                </section>

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $value): ?>
                        <section class="color-red"><?php echo h($value); ?></section>
                    <?php endforeach ?>
                <?php endif ?>

                <section>
                    <?php echo h($loginMessage) ?>
                </section>

                <section class="bgcolor">
                    <article>
                        <p>スタッフ削除</p><br><br>
                        <form method="post" action="check_done.php">
                            <article>
                                <p><?php echo '（' . h($id) . '）' . h($auth) . '　' . h($email) . '　' . h(displayDateWeek($created_at)); ?></p>
                            </article>
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                            <input type="hidden" name="admin_staff_delete_one_token" value="<?php echo h($admin_staff_delete_one_token); ?>">
                            <button class="button" type="submit">削除する</button>
                            <a class="button" href="cancel.php">キャンセル</a>
                        </form>
                    </article>
                    <section>
                        <a class="button" href="cancel.php">戻る</a>
                    </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
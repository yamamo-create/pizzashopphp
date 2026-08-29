<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManagePagecreate.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';

use function App\Common\h;
use function App\Common\displayDateWeek;
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

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

try {
    $productAllData = $adminManagePagecreate->getAdminProductAllData();
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
                    <h2>ページ作成モード</h2>
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
                    <form action="check.php" method="post">
                        <h3>ごはんピザ</h3>
                        <br><br>
                        <?php for ($i = 0; $i < 10; ++$i): ?>
                            <select class="block" name="meal[]">
                                <option value="0" selected>メニューを選んでください</option>
                                <?php foreach ($productAllData as $key => $value): ?>
                                    <?php
                                    $id = $value['id'] ?? '';
                                    $name = $value['name'] ?? '';
                                    $updated_at = $value['updated_at'] ?? '';
                                    $item = '（' . $id . '）' . $name . '　' . displayDateWeek($updated_at);
                                    ?>
                                    <option value="<?php echo h($id) ?>"><?php echo h($item) ?></option>
                                <?php endforeach; ?>
                            </select><br><br>
                        <?php endfor; ?>
                        <br><br>
                        <h3>デザートピザ</h3>
                        <br><br>
                        <?php for ($i = 0; $i < 10; ++$i): ?>
                            <select class="block" name="dessert[]">
                                <option value="0" selected>メニューを選んでください</option>
                                <?php foreach ($productAllData as $key => $value): ?>
                                    <?php
                                    $id = $value['id'] ?? '';
                                    $name = $value['name'] ?? '';
                                    $updated_at = $value['updated_at'] ?? '';
                                    $item = '（' . $id . '）' . $name . '　' . displayDateWeek($updated_at);
                                    ?>
                                    <option value="<?php echo h($id) ?>"><?php echo h($item) ?></option>
                                <?php endforeach; ?>
                            </select><br><br>
                        <?php endfor; ?>
                        <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                        <button class="button" type="submit">OK</button>
                        <a class="button" href="cancel.php">キャンセル</a>
                    </form>
                </section>

                <section>
                    <article>
                        <a class="button" href="cancel.php">戻る</a>
                    </article>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
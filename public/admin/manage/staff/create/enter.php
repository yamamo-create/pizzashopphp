<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Admin/Service/AdminManageStaff.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use function App\Common\h;
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

$createEmail = $_SESSION['admin']['form']['create']['email'] ?? '';

$admin_csrf_token = AdminCsrf::ensure();
$admin_staff_create_one_token = AdminOneTimeToken::generate('admin_staff_create_one_token');
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
                    <h2>スタッフ追加</h2>
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
                        <p>スタッフ追加</p>
                        <br><br>
                        <form method="post" action="check_done.php">
                            <p>
                                <input type="radio" name="auth" value="general" checked>一般
                                <input type="radio" name="auth" value="admin">管理者
                            </p>
                            <br><br>
                            <p>ログインID（Email）</p>
                            <p><input type=" email" name="email" id="email" value="<?php echo h($createEmail); ?>" /></p>
                            <br><br>
                            <p>パスワード</p>
                            <p><input type="password" name="password1" /></p>
                            <p>同じパスワードをもう一度</p>
                            <p><input type="password" name="password2" /></p>
                            <br><br>
                            <input type="hidden" name="admin_staff_create_one_token" value="<?php echo h($admin_staff_create_one_token); ?>">
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                            <p>
                                <button class="button" type="submit">OK</button>
                                <a class="button" href="cancel.php">キャンセル</a>
                            </p>
                        </form>
                    </article>
                </section>
                <section>
                    <p>パスワードは「半角大英字、半角小英字、半角数字を両方利用した（8〜64文字）」でお願いします</p>
                    <p>パスワードは、記号は使えません</p>
                    <p>パスワードは、確認のため「同じパスワードを２回」入力してください</p>
                </section>
                <section>
                    <a class="button" href="cancel.php">戻る</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
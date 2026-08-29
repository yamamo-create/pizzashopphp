<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';

use App\Common\RedirectPage;
use function App\Common\h;
use App\Customer\Validator\CustomerSessionValidator;

session_start();

$sessionValidator = new CustomerSessionValidator();

// ----- ログイン維持用 -----
$is_login = $_SESSION['customer']['login']['is_login'] ?? null;
$login_id = $_SESSION['customer']['login']['id'] ?? null;
$login_email = $_SESSION['customer']['login']['email'] ?? null;
try {
    $sessionValidator->validateIsLogin($is_login);
    $sessionValidator->validateLoginIdEmail($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::customerTimeOutErrPage($e);
}
$loginMessage = "（{$login_id}）{$login_email}さんログイン中";
// ----------

$success = $_SESSION['flash']['success'] ?? null;
unset($_SESSION['flash']['success']);

try {
    $sessionValidator->validateFlashMessage($success);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}
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

        <?php require_once PUBLIC_PATH . '/customer_header.php'; ?>

        <main>
            <div class="main-wrapper">
                <section>
                    <h2>メールアドレスの変更</h2>
                </section>

                <section>
                    <p>メールアドレスの変更が完了しました</p>
                </section>

                <section class="bgcolor">
                    <article>
                        <p>変更後のメールアドレス：<?php echo h($login_email); ?></p><br>
                        <p></p>
                        <p>（注意）</p>
                        <p>メールアドレスはログインに使います</p>
                        <p>変更後のメールアドレスは忘れないようにしてください</p>
                        <br><br>
                    </article>
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
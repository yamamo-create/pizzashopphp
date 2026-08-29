<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';

use function App\Common\h;
use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Token\CustomerCsrf;

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

$customer_csrf_token = CustomerCsrf::ensure();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="customer_csrf_token" content="<?php echo h($customer_csrf_token) ?>" />
    <title>元気pizza</title>
    <link rel="stylesheet" href="../../initialsetting.css" />
    <link rel="stylesheet" href="../../header_footer.css" />
    <link rel="stylesheet" href="../../style.css" />
    <script src="top.js" defer></script>
</head>

<body>
    <div class="body-wrapper">

        <?php require_once PUBLIC_PATH . '/customer_header.php'; ?>

        <main>
            <div class="main-wrapper">

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $value): ?>
                        <section class="color-red"><?php echo h($value); ?></section>
                    <?php endforeach ?>
                <?php endif ?>

                <section>
                    <?php echo h($loginMessage) ?>
                </section>

                <section>
                    <nav class="nav">
                        <ul>
                            <li><a class="button" href="/customer/top/cart/enter.php">カートを見る</a></li>
                            <li><a class="button" href="/customer/his/index.php">購入履歴</a></li>
                            <li><a class="button" href="/customer/view/index.php">アカウント情報</a></li>
                            <li><a class="button" href="/customer/logout/done.php">ログアウト</a></li>
                        </ul>
                    </nav>
                </section>

                <section class="menu">
                    <h2>----- Menu -----</h2>
                </section>

                <section>
                    <h3>ごはんピザ</h3><br><br>
                </section>

                <section class="g-menu" id="gMenu"></section>

                <section>
                    <br><br>
                    <h3>デザートピザ</h3><br><br>
                </section>

                <section class="d-menu" id="dMenu"></section>
                <section>
                    <a class="button" href="/customer/top/cart/enter.php">カートを見る</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
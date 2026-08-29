<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerView.php';

use App\Common\RedirectPage;
use function App\Common\h;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Service\CustomerView;

session_start();

$sessionValidator = new CustomerSessionValidator();
$customerView = new CustomerView();

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
    $customerData = $customerView->getCustomerData($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

$email = $customerData['email'] ?? '';
$lastname = $customerData['lastname'] ?? '';
$firstname = $customerData['firstname'] ?? '';
$phone = $customerData['phone'] ?? '';
$post = $customerData['post'] ?? '';
$address = $customerData['address'] ?? '';
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
                    <h2>登録情報の修正</h2>
                </section>

                <section>
                    <?php echo h($loginMessage) ?>
                </section>

                <section>
                    <p>登録情報を修正しました</p>
                </section>

                <section class="bgcolor">
                    <article>
                        <p>苗字　　：<?php echo h($lastname); ?></p><br>
                        <p>名前　　：<?php echo h($firstname); ?></p><br>
                        <p>電話番号：<?php echo h($phone); ?></p><br>
                        <p>郵便番号：<?php echo h($post); ?></p><br>
                        <p>住所　　：<?php echo h($address); ?></p><br>
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
<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerView.php';

use App\Common\RedirectPage;
use function App\Common\h;
use function App\Common\displayDateWeek;
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

try {
    $customerData = $customerView->getCustomerData($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

$lastname = $customerData['lastname'] ?? '';
$firstname = $customerData['firstname'] ?? '';
$phone = $customerData['phone'] ?? '';
$post = $customerData['post'] ?? '';
$address = $customerData['address'] ?? '';
$created_at = $customerData['created_at'] ?? '';
$updated_at = $customerData['updated_at'] ?? '';
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
                    <h2>登録情報</h2>
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
                    <a class="button" href="/customer/view/edit/enter.php">登録情報の修正</a>
                    <a class="button" href="/customer/view/change_email/enter.php">メールアドレス変更</a>
                    <a class="button" href="/customer/view/change_pass/enter.php">パスワード変更</a>
                </section>

                <section>
                    <a class="button" href="cancel.php">戻る</a>
                </section>

                <section class="bgcolor">
                    <article>
                        <p>メールアドレス：<?php echo h($login_email); ?></p>
                        <p>お名前：<?php echo h($lastname) . '　' . h($firstname); ?></p>
                        <p>電話番号：<?php echo h($phone); ?></p>
                        <p>郵便番号：<?php echo h($post); ?></p>
                        <p>住所：<?php echo h($address); ?></p>
                        <p>登録日：<?php echo h(displayDateWeek($created_at)); ?>　修正日：<?php echo h(displayDateWeek($updated_at)); ?></p>
                    </article>
                </section>

                <section>
                    <a class="button" href="/customer/view/withdraw/enter.php">アカウントを削除する</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
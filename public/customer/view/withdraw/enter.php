<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerView.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';

use App\Common\RedirectPage;
use function App\Common\h;
use function App\Common\displayDateWeek;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Service\CustomerView;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Token\CustomerOneTimeToken;

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

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

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

$customer_csrf_token = CustomerCsrf::ensure();
$withdraw_onetime = CustomerOneTimeToken::generate('withdraw_onetime');
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
                    <h2>アカウント削除</h2>
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
                    <p>以下のアカウントを本当に削除しますか？</p>
                </section>

                <section class="bgcolor">
                    <article>
                        <p>メールアドレス：<?php echo h($login_email); ?></p><br>
                        <p>お名前：<?php echo h($lastname) . '　' . h($firstname); ?></p><br>
                        <p>電話番号：<?php echo h($phone); ?></p><br>
                        <p>郵便番号：<?php echo h($post); ?></p><br>
                        <p>住所：<?php echo h($address); ?></p><br>
                        <p>登録日：<?php echo h(displayDateWeek($created_at)); ?></p><br>
                    </article>
                </section>

                <section>
                    <form method="post" action="check_done.php">
                        <p>パスワードを入力して下さい</p>
                        <input type="password" name="password" />
                        <input type="hidden" name="customer_csrf_token" value="<?php echo h($customer_csrf_token); ?>">
                        <input type="hidden" name="withdraw_onetime" value="<?php echo h($withdraw_onetime); ?>">
                        <button type="submit" class="button">本当にアカウントを削除する</button>
                        <a class="button" href="cancel.php">戻る</a>
                    </form>
                </section>

                <section>
                    <a class="button" href="cancel.php">キャンセル</a>
                </section>

            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerView.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';

use App\Common\RedirectPage;
use function App\Common\h;
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

$email = $customerData['email'] ?? '';
$lastname = $customerData['lastname'] ?? '';
$firstname = $customerData['firstname'] ?? '';
$phone = $customerData['phone'] ?? '';
$post = $customerData['post'] ?? '';
$address = $customerData['address'] ?? '';

$customer_csrf_token = CustomerCsrf::ensure();
$customer_edit_onetime = CustomerOneTimeToken::generate('customer_edit_onetime');
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
                        <form method="post" action="check_done.php">
                            <p>苗字</p>
                            <p><input type="text" name="lastname" value="<?php echo h($lastname); ?>" /></p>
                            <p>名前</p>
                            <p><input type="text" name="firstname" value="<?php echo h($firstname); ?>" /></p>
                            <p>電話番号</p>
                            <p><input type="tel" name="phone" value="<?php echo h($phone); ?>" /></p>
                            <p>郵便番号</p>
                            <p><input type="text" name="post" value="<?php echo h($post); ?>" /></p>
                            <p>住所</p>
                            <p><input type="text" name="address" id="address" value="<?php echo h($address); ?>" /></p>
                            <br><br>
                            <input type="hidden" name="customer_csrf_token" value="<?php echo h($customer_csrf_token); ?>">
                            <input type="hidden" name="customer_edit_onetime" value="<?php echo h($customer_edit_onetime); ?>">
                            <button class="button" type="submit">変更する</button>
                            <a class="button" href="cancel.php">戻る</a>
                        </form>
                    </article>
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
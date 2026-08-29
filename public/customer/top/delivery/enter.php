<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerTop.php';

use function App\Common\h;
use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Service\CustomerTop;

session_start();

$sessionValidator = new CustomerSessionValidator();
$customerTop = new CustomerTop();

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
    $customerData = $customerTop->getCustomerDetail($login_id, $login_email);
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
    <script src="cart.js" defer></script>
</head>

<body>
    <div class="body-wrapper">

        <?php require_once PUBLIC_PATH . '/customer_header.php'; ?>

        <main>
            <div class="main-wrapper">
                <section class="menu">
                    <h2>送り先</h2>
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
                    <p>送り先は、以下でよろしいでしょうか？</p>
                </section>

                <?php
                $id = $customerData['id'] ?? '';
                $email = $customerData['email'] ?? '';
                $lastname = $customerData['lastname'] ?? '';
                $firstname = $customerData['firstname'] ?? '';
                $phone = $customerData['phone'] ?? '';
                $post = $customerData['post'] ?? '';
                $address = $customerData['address'] ?? '';
                ?>

                <section>
                    <p>送り先</p><br><br>
                    <p>お名前　　　　：<?php print h($lastname . '　' . $firstname); ?></p><br>
                    <p>郵便番号　　　：<?php print h($post); ?></p><br>
                    <p>住所　　　　　：<?php print h($address); ?></p><br>
                    <p>電話番号　　　：<?php print h($phone); ?></p><br>
                    <p>メールアドレス：<?php print h($email); ?></p><br>
                </section>

                <section>
                    <a class="button" href="confirm.php">OK</a>
                    <a class="button" href="/customer/top/cart/enter.php">戻る</a>
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
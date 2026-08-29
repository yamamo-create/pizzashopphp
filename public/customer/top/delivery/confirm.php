<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';
require_once SRC_PATH . '/Customer/Service/CustomerTop.php';

use function App\Common\h;
use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Token\CustomerOneTimeToken;
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

$productAllData = [];
$customerData = [];
$totalPrice = 0;

$totalPrice = $_SESSION['customer']['order']['total_price'] ?? null;
$cart = $_SESSION['customer']['cart'] ?? null;

try {
    $sessionValidator->validateTotalPrice($totalPrice);
    $sessionValidator->validateCartIdQuantity($cart);
    $productAllData = $customerTop->getCartProductAllData($cart);
    $customerData = $customerTop->getCustomerDetail($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

$customer_csrf_token = CustomerCsrf::ensure();
$delivery_onetime = CustomerOneTimeToken::generate('delivery_onetime');
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
                    <h2>購入しますか？</h2>
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
                    <p>ご注文の商品</p>
                </section>

                <section class="g-menu" id="gMenu">
                    <?php foreach ($productAllData as $value): ?>
                        <?php
                        $id = $value['id'] ?? '';
                        $name = $value['name'] ?? '';
                        $price = $value['price'] ?? '';
                        $imagename = $value['imagename'] ?? '';
                        $detail = $value['detail'] ?? '';
                        $quantity = $value['quantity'] ?? '';
                        ?>
                        <article
                            data-id="<?php echo h($id) ?>"
                            data-quantity="<?php echo h($quantity) ?>">
                            <img
                                src="/img/<?php echo h($imagename); ?>"
                                alt="<?php echo h($name); ?>" width="150" hight="150" />
                            <p><?php echo h($id); ?></p>
                            <p><?php echo h($name); ?></p>
                            <p><?php echo h($detail); ?></p>
                            <p>
                                １枚：<?php echo h($price); ?>円 ×
                                <?php echo h($quantity); ?>枚 =
                                <?php echo h($price * $quantity); ?>円
                            </p>
                        </article>
                    <?php endforeach ?>
                </section>

                <section>
                    <p>合計金額：<?php echo h($totalPrice); ?></p>
                </section>

                <section>
                    <p>送り先</p>
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
                    <p>お名前　　　　：<?php print h($lastname . '　' . $firstname); ?></p><br>
                    <p>郵便番号　　　：<?php print h($post); ?></p><br>
                    <p>住所　　　　　：<?php print h($address); ?></p><br>
                    <p>電話番号　　　：<?php print h($phone); ?></p><br>
                    <p>メールアドレス：<?php print h($email); ?></p><br>
                </section>

                <section>
                    <form method="post" action="done.php">
                        <input type="hidden" name="customer_csrf_token" value="<?php echo h($customer_csrf_token); ?>">
                        <input type="hidden" name="delivery_onetime" value="<?php echo h($delivery_onetime); ?>">
                        <button class="button" href="done.php">購入する</button>
                        <a class="button" href="/customer/top/cart/enter.php">戻る</a>
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
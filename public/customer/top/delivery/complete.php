<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerTop.php';
require_once SRC_PATH . '/Constants/OrderStatus.php';

use function App\Common\h;
use function App\Common\displayDateWeek;
use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Service\CustomerTop;
use App\Constants\OrderStatus;

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

$order = [];
$orderitem = [];
$customer = [];

$buyNowOrderId = $_SESSION['customer']['order']['order_id'] ?? null;
$success = $_SESSION['flash']['success'] ?? null;
unset($_SESSION['customer']['order']['order_id']);
unset($_SESSION['flash']);

try {
    $sessionValidator->validateOrderId($buyNowOrderId);
    $sessionValidator->validateFlashMessage($success);
    $order = $customerTop->getOrderData($buyNowOrderId);
    $orderitem = $customerTop->getOrderitemData($buyNowOrderId);
    $customer = $customerTop->getCustomerData($login_id, $login_email);
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
                    <h2>ご購入ありがとうございます</h2>
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
                    <p>ご購入の商品</p>
                </section>

                <?php
                $status = OrderStatus::LABELS[$order['status']];
                $totalPrice = $order['total_price'] ?? '';
                $created_at = displayDateWeek($order['created_at'] ?? '');
                ?>

                <section>
                    <article>
                        <p>注文番号：<?php echo h($buyNowOrderId); ?></p>
                        <p>現在状態：<?php echo h($status); ?></p>
                        <p>合計金額：<?php echo h($totalPrice); ?></p>
                        <p>注文日時：<?php echo h($created_at); ?></p>
                    </article>
                </section>

                <section>
                    <article>
                        <?php foreach ($orderitem as $value): ?>
                            <?php
                            $productName = $value['product_name'] ?? '';
                            $productPrice = $value['product_price'] ?? '';
                            $productQuantity = $value['product_quantity'] ?? '';
                            ?>
                            <p>・・・・・・・・・・</p>
                            <p>商品名：<?php echo h($productName); ?></p>
                            <p>値段　：<?php echo h($productPrice); ?></p>
                            <p>数量　：<?php echo h($productQuantity); ?></p>
                        <?php endforeach ?>
                    </article>
                </section>

                <?php $totalPrice = $order['total_price'] ?? ''; ?>

                <section>
                    <p>合計金額：<?php echo h($totalPrice); ?></p>
                </section>

                <section>
                    <p>送り先</p>
                </section>

                <?php
                $id = $customer['id'] ?? '';
                $email = $customer['email'] ?? '';
                $lastname = $customer['lastname'] ?? '';
                $firstname = $customer['firstname'] ?? '';
                $phone = $customer['phone'] ?? '';
                $post = $customer['post'] ?? '';
                $address = $customer['address'] ?? '';
                ?>

                <section>
                    <article>
                        <p>お名前　　　　：<?php print h($lastname . '　' . $firstname); ?></p>
                        <p>郵便番号　　　：<?php print h($post); ?></p>
                        <p>住所　　　　　：<?php print h($address); ?></p>
                        <p>電話番号　　　：<?php print h($phone); ?></p>
                        <p>メールアドレス：<?php print h($email); ?></p>
                    </article>
                </section>

                <section>
                    <a class="button" href="cancel.php">確認</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
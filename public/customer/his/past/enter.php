<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Service/CustomerHis.php';

use App\Common\RedirectPage;
use function App\Common\h;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Service\CustomerHis;

session_start();

$sessionValidator = new CustomerSessionValidator();
$customerHis = new CustomerHis();

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

$customerOrderData = [];

try {
    $customerOrderData = $customerHis->getCompletedCustomerOrder($login_id);
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
                    <h2>購入履歴</h2>
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
                    <a class="button" href="cancel.php">戻る</a>
                </section>

                <section class="bgcolor">
                    <article>
                        <?php foreach ($customerOrderData as $orderId => $value): ?>
                            <?php
                            $status = $value['status'] ?? '';
                            $totalPrice = $value['total_price'] ?? '';
                            $createdAt = $value['created_at'] ?? '';

                            $productData = $value['item'] ?? [];
                            ?>
                            <p>ーーーーーーーーーー</p><br>
                            <p>注文番号：<?php echo h($orderId); ?></p><br>
                            <p>注文日時：<?php echo h($createdAt); ?></p><br>
                            <p>注文状態：<?php echo h($status); ?></p><br>
                            <p>購入合計：<?php echo h($totalPrice); ?></p><br>
                            <p>・・・・・・・・・・</p><br>

                            <?php foreach ($productData as $product): ?>
                                <?php
                                $productName = $product['product_name'] ?? '';
                                $productPrice = $product['product_price'] ?? '';
                                $quantity = $product['product_quantity'] ?? '';
                                $productTotalPrice = $productPrice * $quantity;
                                ?>
                                <p>商品名：<?php echo h($productName); ?></p><br>
                                <p>価格　：<?php echo h($productPrice); ?></p><br>
                                <p>数量　：<?php echo h($quantity); ?></p><br>
                                <p>小計　：<?php echo h($productTotalPrice); ?></p><br>
                                <p>・・・・・・・・・・</p><br>

                            <?php endforeach ?>
                        <?php endforeach ?>
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
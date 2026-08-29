<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/OrderStatus.php';
require_once SRC_PATH . '/Admin/Service/AdminOrder.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use function App\Common\h;
use function App\Common\displayDateWeek;
use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\OrderStatus;
use App\Admin\Service\AdminOrder;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminOrder = new AdminOrder();

// ----- ログイン維持用 -----
$is_login = $_SESSION['admin']['login']['is_login'] ?? '';
$login_id = $_SESSION['admin']['login']['id'] ?? '';
$login_email = $_SESSION['admin']['login']['email'] ?? '';
try {
    $sessionValidator->validateIsLogin($is_login);
    $sessionValidator->validateLoginIdEmail($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::adminTimeOutErrPage($e);
}
$loginMessage = "（{$login_id}）{$login_email}さんログイン中";
// ----------

$errors = $_SESSION['flash']['errors'] ?? '';
unset($_SESSION['flash']['errors']);

$orderData = [];

try {
    $orderData = $adminOrder->getCurrentOrder();
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$admin_csrf_token = AdminCsrf::ensure();
$admin_order_one_token = AdminOneTimeToken::generate('admin_order_one_token');
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

        <?php require_once PUBLIC_PATH . '/admin_header.php'; ?>

        <main>
            <div class="main-wrapper">
                <section>
                    <h2>注文一覧</h2>
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
                        <form method="post" action="check_done.php">
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                            <input type="hidden" name="admin_order_one_token" value="<?php echo h($admin_order_one_token); ?>">
                            <button class="button" type="submit" name="choice" value="received">受付</button>
                            <button class="button" type="submit" name="choice" value="working">作業中</button>
                            <button class="button" type="submit" name="choice" value="shipped">発送完了</button>
                            <button class="button" type="submit" name="choice" value="completed">完了</button>
                            <br><br>
                            <?php foreach ($orderData as $orderId => $value): ?>
                                <?php
                                $status = $value['status'] ?? '';
                                $totalPrice = $value['total_price'] ?? '';
                                $createdAt = $value['created_at'] ?? '';

                                $customerId = $value['customer_id'] ?? '';
                                $email = $value['email'] ?? '';
                                $lastname = $value['lastname'] ?? '';
                                $firstname = $value['firstname'] ?? '';
                                $phone = $value['phone'] ?? '';
                                $post = $value['post'] ?? '';
                                $address = $value['address'] ?? '';

                                $product = $value['item'] ?? '';
                                ?>
                                <p> ーーーーーーーーーー</p>
                                <p><input type="radio" name="choice_id" value="<?php echo h($orderId); ?>"></p>
                                <p>注文番号：<?php echo h($orderId) ?></p>
                                <p>注文日時：<?php echo h(displayDateWeek($createdAt)) ?></p>
                                <p>注文状態：<?php echo h(OrderStatus::LABELS[$status] ?? '') ?></p>
                                <p>合計金額：<?php echo h($totalPrice) ?></p>
                                <p>・・・・・・・・・・</p>
                                <p>顧客番号：<?php echo h($customerId) ?></p>
                                <p>名前　　：<?php echo h($lastname . ' ' . $firstname) ?></p>
                                <p>郵便番号：<?php echo h($post) ?></p>
                                <p>住所　　：<?php echo h($address) ?></p>
                                <p>電話番号：<?php echo h($phone) ?></p>
                                <p>メール　：<?php echo h($email) ?></p>
                                <p>・・・・・・・・・・</p>

                                <?php foreach ($product as $key => $data): ?>
                                    <?php
                                    $productName = $data['product_name'] ?? '';
                                    $productPrice = $data['product_price'] ?? '';
                                    $productQuantity = $data['product_quantity'] ?? '';
                                    ?>
                                    <p> （<?php echo h($key + 1) ?>）</p>
                                    <p>商品名：<?php echo h($productName) ?></p>
                                    <p>数量　：<?php echo h($productQuantity) ?>個</p>
                                    <p>価格　：<?php echo h($productPrice) ?>円</p>
                                    <p>・・・・・・・・・・</p>
                                <?php endforeach ?>
                            <?php endforeach ?>
                        </form>
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
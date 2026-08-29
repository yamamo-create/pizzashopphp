<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Constants/CustomerStatus.php';
require_once SRC_PATH . '/Constants/OrderStatus.php';
require_once SRC_PATH . '/Admin/Service/AdminManageCustomer.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use function App\Common\h;
use function App\Common\displayDateWeek;
use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Constants\CustomerStatus;
use App\Constants\OrderStatus;
use App\Admin\Service\AdminManageCustomer;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminManageCustomer = new AdminManageCustomer();

// ----- ログイン維持用 -----
$is_login = $_SESSION['admin']['login']['is_login'] ?? null;
$login_id = $_SESSION['admin']['login']['id'] ?? null;
$login_email = $_SESSION['admin']['login']['email'] ?? null;
try {
    $sessionValidator->validateIsLogin($is_login);
    $sessionValidator->validateLoginIdEmail($login_id, $login_email);
} catch (Throwable $e) {
    RedirectPage::adminTimeOutErrPage($e);
}
$loginMessage = "（{$login_id}）{$login_email}さんログイン中";
// ----------

// ----- 管理者権限 -----
if (
    $_SESSION['admin']['login']['auth'] !== AdminRole::ADMIN &&
    $_SESSION['admin']['login']['auth'] !== AdminRole::SUPER
) {
    RedirectPage::adminErrPage(__FILE__ . __LINE__ . 'Not Admin Super');
}
// ----------

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

$choiceId = empty($_SESSION['admin']['form']['customer']) ? null : intval($_SESSION['admin']['form']['customer']);

try {
    $sessionValidator->validateCustomerChoiceId($choiceId);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$customerData = [];
$currentOrder = [];
$pastOrder = [];

try {
    $customerData = $adminManageCustomer->getCustomerData($choiceId);
    $currentOrder = $adminManageCustomer->getCurrentOrderData($choiceId);
    $pastOrder = $adminManageCustomer->getPastOrderData($choiceId);
} catch (PDOException $e) {
    RedirectPage::adminErrPage($e);
}

$customerlist_one_token = AdminOneTimeToken::generate('customerlist_one_token');
$admin_csrf_token = AdminCsrf::ensure();
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
                    <h2>顧客詳細</h2>
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
                    <a class="button" href="enter.php">戻る</a>
                </section>

                <section class="bgcolor">
                    <article>
                        <?php
                        $customerId = $customerData['id'] ?? '';
                        $email = $customerData['email'] ?? '';
                        $lastname = $customerData['lastname'] ?? '';
                        $firstname = $customerData['firstname'] ?? '';
                        $phone = $customerData['phone'] ?? '';
                        $post = $customerData['post'] ?? '';
                        $address = $customerData['address'] ?? '';
                        $status = $customerData['status'] ?? '';
                        $created_at = empty($customerData['created_at']) ? '' : displayDateWeek($customerData['created_at']);
                        $updated_at = empty($customerData['updated_at']) ? '' : displayDateWeek($customerData['updated_at']);
                        $deleted_at = empty($customerData['deleted_at']) ? '' : displayDateWeek($customerData['deleted_at']);

                        ?>
                        <p>顧客ID：<?php echo h($customerId); ?></p>
                        <p>氏名　　：<?php echo h($lastname) . ' ' . h($firstname); ?></p>
                        <p>郵便番号：<?php echo h($post); ?></p>
                        <p>住所　　：<?php echo h($address); ?></p>
                        <p>電話番号：<?php echo h($phone); ?></p>
                        <p>メール　：<?php echo h($email); ?></p>
                        <p></p>
                        <p>作成日　：<?php echo h($created_at); ?></p>
                        <p>更新日　：<?php echo h($updated_at); ?></p>
                        <p>削除日　：<?php echo h($deleted_at); ?></p>
                        <p></p>
                        <p>状態　　：<?php echo h(CustomerStatus::LABELS[$status] ?? ''); ?></p>
                        <br><br>
                        <form method="post" action="done.php">
                            <input type="hidden" name="customerlist_one_token" value="<?php echo h($customerlist_one_token); ?>">
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                            <button class="button" type="submit" name="choice" value="possible">アカウントを利用可能にする</button>
                            <button class="button" type="submit" name="choice" value="stop">アカウントを停止させる</button>
                        </form>
                        <br><br>
                    </article>
                </section>

                <section>
                    <article>
                        <h3>未完了の注文</h3>
                        <?php foreach ($currentOrder as $orderId => $value): ?>
                            <?php
                            $status = $value['status'] ?? '';
                            $totalPrice = $value['total_price'] ?? '';
                            $createdAt = $value['created_at'] ?? '';

                            $productData = $value['item'] ?? [];
                            ?>
                            <p>ーーーーーーーーーー</p><br>
                            <p>注文番号：<?php echo h($orderId); ?></p><br>
                            <p>注文日時：<?php echo h(displayDateWeek($createdAt)); ?></p><br>
                            <p>注文状態：<?php echo h(OrderStatus::LABELS[$status]); ?></p><br>
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
                    <article>
                        <h3>完了した注文</h3>
                        <?php foreach ($pastOrder as $orderId => $value): ?>
                            <?php
                            $status = $value['status'] ?? '';
                            $totalPrice = $value['total_price'] ?? '';
                            $createdAt = $value['created_at'] ?? '';

                            $productData = $value['item'] ?? [];
                            ?>
                            <p>ーーーーーーーーーー</p><br>
                            <p>注文番号：<?php echo h($orderId); ?></p><br>
                            <p>注文日時：<?php echo h($createdAt); ?></p><br>
                            <p>注文状態：<?php echo h(OrderStatus::LABELS[$status]); ?></p><br>
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
                    <a class="button" href="enter.php">戻る</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
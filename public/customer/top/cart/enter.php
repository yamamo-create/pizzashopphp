<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Service/CustomerTop.php';

use function App\Common\h;
use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Token\CustomerCsrf;
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

if (empty($_SESSION['customer']['cart'])) {
    header('Location: cancel.php');
    exit();
}

$productAllData = [];

$cart = $_SESSION['customer']['cart'] ?? [];

try {
    $sessionValidator->validateCartIdQuantity($cart);
    $productAllData = $customerTop->getCartProductAllData($cart);
} catch (Throwable $e) {
    RedirectPage::customerErrPage($e);
}

$customer_csrf_token = CustomerCsrf::ensure();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="customer_csrf_token" content="<?php print h($customer_csrf_token); ?>" />
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
                    <h2>カート</h2>
                </section>

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $value): ?>
                        <section class="color-red"><?php echo h($value); ?></section>
                    <?php endforeach; ?>
                <?php endif; ?>

                <section>
                    <?php echo h($loginMessage) ?>
                </section>

                <section>
                    <p>数量を決めて下さい</p>
                </section>

                <section class="g-menu" id="gMenu">
                    <?php foreach ($productAllData as $value): ?>
                        <?php
                        $id = $value['id'];
                        $name = $value['name'];
                        $pricee = $value['price'];
                        $imagename = $value['imagename'];
                        $detail = $value['detail'];
                        $quantity = $value['quantity'];
                        ?>
                        <article
                            data-id="<?php echo h($id); ?>"
                            data-quantity="<?php echo h($quantity); ?>">
                            <img
                                src="/img/<?php echo h($imagename); ?>"
                                alt="<?php echo h($name); ?>" width="150" hight="150" />
                            <p><?php echo h($id); ?></p>
                            <p><?php echo h($name); ?></p>
                            <p><?php echo h($detail); ?></p>
                            <p>１枚：<?php echo h($pricee); ?>円</p>
                            <span>
                                <button class="inc button">+</button>
                                <input type="text" class="quantitytext" value="<?php echo h($quantity); ?>" readonly>
                                <button class="dec button">-</button><br><br>
                                <button class="delete button">カートから削除</button>
                            </span>
                        </article>
                    <?php endforeach; ?>
                </section>

                <section>
                    <a class="button" href="confirm.php">確認する</a>
                    <a class="button" href="cancel.php">戻る</a>
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
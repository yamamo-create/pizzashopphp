<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManageProduct.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use function App\Common\h;
use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManageProduct;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminManageProduct = new AdminManageProduct();

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

$choiceId = $_SESSION['admin']['system']['choice_id'] ?? null;

try {
    $sessionValidator->validateSystemChoiceId($choiceId);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

try {
    $productData = $adminManageProduct->getAdminProductData($choiceId);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$id = $productData['id'] ?? '';
$name = $productData['name'] ?? '';
$price = $productData['price'] ?? '';
$imagename = $productData['imagename'] ?? '';
$detail = $productData['detail'] ?? '';
$updated_at = $productData['updated_at'] ?? '';

$admin_csrf_token = AdminCsrf::ensure();
$admin_product_update_one_token = AdminOneTimeToken::generate('admin_product_update_one_token');
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
                    <h2>商品の更新</h2>
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
                        <form enctype="multipart/form-data" method="post" action="check_done.php">
                            <p>商品名</p>
                            <p><input type="text" name="name" id="name" value="<?php echo h($name) ?>" /></p>
                            <p>価格</p>
                            <p><input type="number" name="price" id="price" value="<?php echo h($price) ?>" /></p>
                            <br><br>
                            <p>画像</p>
                            <p><input type="file" name="image" accept="image/png" /></p>
                            <br><br>
                            <p>商品説明</p>
                            <p><input type="text" name="detail" id="detail" value="<?php echo h($detail) ?>" /></p>
                            <br><br>
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                            <input type="hidden" name="admin_product_update_one_token" value="<?php echo h($admin_product_update_one_token); ?>">
                            <p>
                                <button class="button" type="submit">OK</button>
                                <a class="button" href="cancel.php">キャンセル</a>
                            </p>
                        </form>
                    </article>
                </section>

                <section>
                    <p>画像は、150*150ピクセルでお願いします。</p>
                    <p>画像の拡張子はPNGでお願いします。</p>
                    <p>説明は「全角文字、全角スペース外の文字は使えません</p>
                    <p>説明の文字数は「全角文字、18文字以内でお願いします」</p>
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
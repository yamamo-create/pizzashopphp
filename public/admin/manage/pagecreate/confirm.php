<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManagePagecreate.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Token/AdminOneTimeToken.php';

use function App\Common\h;
use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManagePagecreate;
use App\Admin\Token\AdminCsrf;
use App\Admin\Token\AdminOneTimeToken;

session_start();

$sessionValidator = new AdminSessionValidator();
$adminManagePagecreate = new AdminManagePagecreate();

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

$meal = $_SESSION['admin']['form']['page']['meal'] ?? null;
$dessert = $_SESSION['admin']['form']['page']['dessert'] ?? null;

try {
    $sessionValidator->validatePagecreatePuroductId($meal);
    $sessionValidator->validatePagecreatePuroductId($dessert);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$mealData = [];
$dessertData = [];

try {
    $mealData = $adminManagePagecreate->getProductDatas($meal);
    $dessertData = $adminManagePagecreate->getProductDatas($dessert);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

$admin_csrf_token = AdminCsrf::ensure();
$admin_pagecreate_one_token = AdminOneTimeToken::generate('admin_pagecreate_one_token');
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
                <section class="menu">
                    <h2>ページ作成モード</h2>
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
                    <article>
                        <h3>ごはんピザ</h3>
                        <br><br>
                    </article>
                </section>

                <section class="g-menu" id="gMenu">
                    <?php foreach ($mealData as $value): ?>
                        <article>
                            <?php
                            $id = $value['id'] ?? '';
                            $name = $value['name'] ?? '';
                            $price = $value['price'] ?? '';
                            $imagename = $value['imagename'] ?? '';
                            $detail = $value['detail'] ?? '';
                            ?>
                            <div><?php print h($name); ?></div>
                            <div>
                                <img src="/img/<?php print h($imagename); ?>" alt="ピザの写真" />
                                <ul>
                                    <li><?php print h($detail); ?></li>
                                    <li>１枚：<?php print h($price); ?>円</li>
                                </ul>
                            </div>
                            <div>
                                <a class="cart button">カートに入れる</a>
                            </div>
                        </article>
                    <?php endforeach ?>
                </section>

                <section>
                    <article>
                        <br><br>
                        <h3>デザートピザ</h3>
                        <br><br>
                    </article>
                </section>

                <section class="d-menu" id="dMenu">
                    <?php foreach ($dessertData as $value): ?>
                        <article>
                            <?php
                            $id = $value['id'] ?? '';
                            $name = $value['name'] ?? '';
                            $price = $value['price'] ?? '';
                            $imagename = $value['imagename'] ?? '';
                            $detail = $value['detail'] ?? '';
                            ?>
                            <div><?php print h($name); ?></div>
                            <div>
                                <img src="/img/<?php print h($imagename); ?>" alt="ピザの写真" />
                                <ul>
                                    <li><?php print h($detail); ?></li>
                                    <li>１枚：<?php print h($price); ?>円</li>
                                </ul>
                            </div>
                            <div>
                                <a class="cart button">カートに入れる</a>
                            </div>
                        </article>
                    <?php endforeach ?>
                </section>

                <section>
                    <article>
                        <form action="done.php" method="post">
                            <p>こちらの内容で確定しますか？</p>
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
                            <input type="hidden" name="admin_pagecreate_one_token" value="<?php echo h($admin_pagecreate_one_token); ?>">
                            <button class="button" type="submit">OK</button>
                            <a class="button" href="enter.php">キャンセル</a>
                        </form>
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
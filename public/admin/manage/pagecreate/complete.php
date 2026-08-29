<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Admin/Validator/AdminSessionValidator.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Service/AdminManagePagecreate.php';

use function App\Common\h;
use App\Common\RedirectPage;
use App\Admin\Validator\AdminSessionValidator;
use App\Constants\AdminRole;
use App\Admin\Service\AdminManagePagecreate;

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

$success = $_SESSION['flash']['success'] ?? null;
unset($_SESSION['flash']['success']);

try {
    $sessionValidator->validateFlashMessage($success);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

try {
    $mealJson = $adminManagePagecreate->getJsonData('meal.json');
    $dessertJson = $adminManagePagecreate->getJsonData('dessert.json');
    $meal = $adminManagePagecreate->decodeJson($mealJson);
    $dessert = $adminManagePagecreate->decodeJson($dessertJson);
} catch (Throwable $e) {
    RedirectPage::adminErrPage($e);
}

if (is_null($meal) || is_null($dessert)) {
    RedirectPage::adminErrPage(
        __FILE__ . 'line:' . __LINE__ . ' Cannot decode JSON.'
    );
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

        <?php require_once PUBLIC_PATH . '/admin_header.php'; ?>

        <main>
            <div class="main-wrapper">
                <section>
                    <h2>ページが作成されました</h2>
                </section>
                <section>
                    <h2>----- Menu -----</h2>
                </section>

                <section>
                    <article>
                        <h3>ごはんピザ</h3>
                        <br><br>
                    </article>
                </section>

                <section class="g-menu" id="gMenu">
                    <?php foreach ($meal as $value): ?>
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
                                <button class="cart button">カートに入れる</button>
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
                    <?php foreach ($dessert as $value): ?>
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
                                <button class="cart button">カートに入れる</button>
                            </div>
                        </article>
                    <?php endforeach ?>
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
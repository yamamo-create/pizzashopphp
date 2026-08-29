<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';

use App\Common\RedirectPage;
use App\Customer\Validator\CustomerSessionValidator;

session_start();

$sessionValidator = new CustomerSessionValidator();

$success = $_SESSION['flash']['success'] ?? null;
unset($_SESSION['flash']['success']);

try {
    $sessionValidator->validateFlashMessage($success);
} catch (Throwable $e) {
    RedirectPage::customerErrPage(__FILE__ . 'line:' . __LINE__ . ' Timeout OR Direct URL');
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
                    <h2>新規登録</h2>
                </section>

                <section class="bgcolor">
                    <article>
                        <p>登録が完了しました</p>
                    </article>
                </section>

                <section>
                    <a class="button" href="/customer/top/index.php">商品ページに進む</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
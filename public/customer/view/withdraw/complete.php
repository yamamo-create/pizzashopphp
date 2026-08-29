<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';

use App\Common\RedirectPage;

session_start();

if (!isset($_SESSION['flash']['success'])) {
    RedirectPage::customerTimeOutErrPage(
        __FILE__ . 'line:' . __LINE__ . ' timeout OR direct URL'
    );
}
unset($_SESSION['flash']['success']);

$_SESSION = [];
if (isset($_COOKIE[session_name()])) {
    setcookie(
        session_name(),
        '',
        time() - 42000,
        '/'
    );
}
session_destroy();
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
                    <h2>アカウント削除</h2>
                    <p>アカウントは削除されました</p>
                </section>

                <section class="bgcolor">
                    <article>
                        <p>再度ご利用の際は、もう一度新規登録をお願いします。</p>
                        <p>アカウントの復活はできません。</p>
                        <p>ありがとうございました。</p>
                    </article>
                </section>

                <section>
                    <a class="button" href="/index.php">トップページ</a>
                </section>

            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
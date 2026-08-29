<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
?>
<!doctype html>
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
                    <h2>タイムアウトしました</h2>
                </section>
                <section class="bgcolor">
                    <article>
                        <p>ログインをやり直して下さい</p><br /><br />
                        <a class="button" href="/index.php">トップ画面に戻る</a>
                    </article>
                </section>
                <section></section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
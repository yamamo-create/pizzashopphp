<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
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
                    <p></p>
                </section>
                <section class="bgcolor">
                    <article>
                        <p>ログアウトしました</p><br><br>
                        <a class="button" href="/admin/login/enter.php">トップへ戻る</a><br>
                    </article>
                </section>
                <section>
                    <p></p>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';

use function App\Common\h;
use App\Customer\Token\CustomerCsrf;

session_start();

$email = $_SESSION['customer']['form']['login']['email'] ?? '';

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

$customer_csrf_token = CustomerCsrf::ensure();
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
                    <h2>ログイン</h2>
                </section>

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $value): ?>
                        <section class="color-red"><?php echo h($value); ?></section>
                    <?php endforeach; ?>
                <?php endif; ?>

                <section class="bgcolor">
                    <article>
                        <form method="post" action="check_done.php">
                            <p>Email（ I D ）</p>
                            <p><input type="text" name="email" value="<?php echo h($email); ?>" /></p>
                            <p>パスワード</p>
                            <p><input type="password" name="password" /></p>
                            <p><input type="hidden" name="customer_csrf_token" value="<?php echo h($customer_csrf_token); ?>"></p>
                            <br><br>
                            <p>
                                <button class="button" type="submit">ログイン</button>
                                <a class="button" href="cancel.php">キャンセル</a>
                            </p>
                        </form>
                    </article>
                </section>

                <section>
                    <p>登録したEmail（ID）とパスワードを入力してログインを押してください</p>
                </section>

                <section>
                    <a class="button" href="cancel.php">トップへ戻る</a>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
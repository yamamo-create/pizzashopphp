<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';

use function App\Common\h;
use App\Admin\Token\AdminCsrf;

session_start();

$email = $_SESSION['admin']['form']['login']['email'] ?? '';

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

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
                    <h2>管理者ログイン</h2>
                </section>

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $value): ?>
                        <section class="color-red"><?php echo h($value); ?></section>
                    <?php endforeach ?>
                <?php endif ?>

                <section class="bgcolor">
                    <article>
                        <form method="post" action="check_done.php">
                            <p>Email(ID)</p>
                            <p><input type="text" name="email" value="<?php echo h($email); ?>" /></p>
                            <p>パスワード</p>
                            <p><input type="password" name="password" value="" /></p>
                            <br /><br />
                            <input type="hidden" name="admin_csrf_token" value="<?php echo h($admin_csrf_token); ?>">
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
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
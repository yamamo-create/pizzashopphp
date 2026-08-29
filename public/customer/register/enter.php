<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';
require_once SRC_PATH . '/Customer/Validator/CustomerSessionValidator.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Token/CustomerOneTimeToken.php';

use function App\Common\h;
use App\Customer\Validator\CustomerSessionValidator;
use App\Customer\Token\CustomerCsrf;
use App\Customer\Token\CustomerOneTimeToken;

session_start();

$sessionValidator = new CustomerSessionValidator();

$email = $_SESSION['customer']['form']['register']['email'] ?? '';
$lastname = $_SESSION['customer']['form']['register']['lastname'] ?? '';
$firstname = $_SESSION['customer']['form']['register']['firstname'] ?? '';
$phone = $_SESSION['customer']['form']['register']['phone'] ?? '';
$post = $_SESSION['customer']['form']['register']['post'] ?? '';
$address = $_SESSION['customer']['form']['register']['address'] ?? '';

unset($_SESSION['customer']['form']['register']);

$errors = $_SESSION['flash']['errors'] ?? null;
unset($_SESSION['flash']['errors']);

$customer_csrf_token = CustomerCsrf::ensure();
$register_onetime = CustomerOneTimeToken::generate('register_onetime');
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

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $value): ?>
                        <section class="color-red"><?php echo h($value); ?></section>
                    <?php endforeach ?>
                <?php endif ?>

                <section>
                    <p>新しくアカウントを登録します</p>
                </section>

                <section class="bgcolor">
                    <article>
                        <form method="post" action="check_done.php">
                            <p>ログインID（Email）</p>
                            <p><input type="email" name="email" value="<?php echo h($email); ?>" /></p>
                            <p>パスワード</p>
                            <p><input type="password" name="password1" /></p>
                            <p>同じパスワードをもう一度</p>
                            <p><input type="password" name="password2" /></p>
                            <p>苗字</p>
                            <p><input type="text" name="lastname" value="<?php echo h($lastname); ?>" /></p>
                            <p>名前</p>
                            <p><input type="text" name="firstname" value="<?php echo h($firstname); ?>" /></p>
                            <p>電話番号</p>
                            <p><input type="tel" name="phone" value="<?php echo h($phone); ?>" /></p>
                            <p>郵便番号</p>
                            <p><input type="text" name="post" value="<?php echo h($post); ?>" /></p>
                            <p>住所</p>
                            <p><input type="text" name="address" id="address" value="<?php echo h($address); ?>" /></p>
                            <br><br>
                            <p>
                                <button class="button" type="submit">登録する</button>
                                <a class="button" href="/index.php">キャンセル</a>
                            </p>
                            <input type="hidden" name="register_onetime" value="<?php echo h($register_onetime); ?>">
                            <input type="hidden" name="customer_csrf_token" value="<?php echo h($customer_csrf_token); ?>">
                        </form>
                    </article>
                </section>

                <section>
                    <article>
                        <p>パスワードは「半角大英字、半角小英字、半角数字を両方利用した（8〜64文字）」でお願いします</p>
                        <p>パスワードは、記号は使えません</p>
                        <p>パスワードは、確認のため「同じパスワードを２回」入力してください</p>
                    </article>
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
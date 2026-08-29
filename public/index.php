<?php
require_once __DIR__ . '/../src/Config/Path.php';
require_once SRC_PATH . '/Common/RedirectPage.php';
require_once SRC_PATH . '/Common/Htmlspecialchars.php';

use App\Common\RedirectPage;
use function App\Common\h;

$jsonMeal = file_get_contents(JSON_PATH . '/meal.json');
$jsonDessert = file_get_contents(JSON_PATH . '/dessert.json');

if ($jsonMeal === false || $jsonDessert === false) {
    RedirectPage::customerErrPage(
        __FILE__ . 'line:' . __LINE__ . ' JSON file not found'
    );
}

$arrayMeal = json_decode($jsonMeal, true);
$arrayDessert = json_decode($jsonDessert, true);

if (is_null($arrayMeal) || is_null($arrayDessert)) {
    RedirectPage::customerErrPage(
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

        <?php require_once PUBLIC_PATH . '/customer_header.php'; ?>

        <main>
            <div class="main-wrapper">
                <section>
                    <a class="button" href="/customer/login/enter.php">ログイン</a>
                    <a class="button" href="/customer/register/enter.php">新規登録</a>
                </section>

                <section class="menu">
                    <h2>----- Menu -----</h2>
                </section>

                <section>
                    <article>
                        <h3>ごはんピザ</h3>
                        <br><br>
                    </article>
                </section>

                <section class="g-menu" id="gMenu">
                    <?php for ($i = 0; $i < count($arrayMeal); ++$i): ?>
                        <article>
                            <?php
                            $id = $arrayMeal[$i]['id'] ?? '';
                            $name = $arrayMeal[$i]['name'] ?? '';
                            $price = $arrayMeal[$i]['price'] ?? '';
                            $filename = $arrayMeal[$i]['imagename'] ?? '';
                            $detail = $arrayMeal[$i]['detail'] ?? '';
                            ?>
                            <div><?php print h($name); ?></div>
                            <div>
                                <img src="/img/<?php print h($filename); ?>" alt="ピザの写真" />
                                <ul>
                                    <li><?php print h($detail); ?></li>
                                    <li>１枚：<?php print h($price); ?>円</li>
                                </ul>
                            </div>
                        </article>
                    <?php endfor ?>
                </section>

                <section>
                    <article>
                        <br><br>
                        <h3>デザートピザ</h3>
                        <br><br>
                    </article>
                </section>

                <section class="d-menu" id="dMenu">
                    <?php for ($i = 0; $i < count($arrayDessert); ++$i): ?>
                        <article>
                            <?php
                            $id = $arrayDessert[$i]['id'] ?? '';
                            $name = $arrayDessert[$i]['name'] ?? '';
                            $price = $arrayDessert[$i]['price'] ?? '';
                            $filename = $arrayDessert[$i]['imagename'] ?? '';
                            $detail = $arrayDessert[$i]['detail'] ?? '';
                            ?>
                            <div><?php print h($name); ?></div>
                            <div>
                                <img src="/img/<?php print h($filename); ?>" alt="ピザの写真" />
                                <ul>
                                    <li><?php print h($detail); ?></li>
                                    <li>１枚：<?php print h($price); ?>円</li>
                                </ul>
                            </div>
                        </article>
                    <?php endfor ?>
                </section>
            </div>
        </main>

        <?php require_once PUBLIC_PATH . '/footer.php'; ?>

    </div>
</body>

</html>
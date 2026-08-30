# PHP EC Site

## 概要

PHPで作成した「ECサイト」です。<br>
<br>
フレームワークは使用せず、<br>
MVCを意識して設計しました。<br>
<br>
顧客向け画面だけでなく、<br>
管理側の機能も実装しています。<br>
<br>
セキュリティ、保守性を意識して開発しました。<br>
<br>
<pre>
MVC = Model、View、Controller

Model      = データ管理
View       = 画面表示
Controller = Model、Viewに指示を出す
</pre>

## 制作期間

2026年3月〜2026年8月（約6か月）<br>

## デモ画像

画像は「chatGPT」により作成。<br>
<br>
メールアドレス、IPアドレスは、一部を消去しています。<br>

### 顧客側

「商品一覧」<br>
<br>
未ログインは、商品一覧。<br>
ログイン後は、カート機能が使えます。<br>
<br>
<img src="images/2.png"><br>
<br><br>
<img src="images/3.png"><br>
<br><br>
<img src="images/4.png"><br>
<br><br>
<img src="images/5.png"><br>
<br><br>
<img src="images/6.png"><br>
<br><br>
<img src="images/7.png">

### 管理側

「管理」

管理は、アカウントを権限によって2種類に分けています。<br>
<br>
１：全ての操作（admin）<br>
２：一部の操作（general）<br>
<br><br>
<img src="images/11.png"><br>
<br><br>
<img src="images/12.png"><br>
<br><br>
<img src="images/13.png"><br>
<br><br>
<img src="images/14.png">

## 開発目的

PHP学習のため、<br>
フレームワークを使用せず<br>
一からECサイトを制作しました。<br>
<br>
「セキュリティ」<br>
「保守性を意識した設計」<br>
「データベース設計」<br>
<br>
など、学ぶことを目的としています。

## 主な機能

### 顧客

- 会員登録
- ログイン
- 商品一覧
- カート
- 注文
- 注文履歴
- アカウント管理

### 管理者

- 商品管理
- ページ管理
- スタッフ管理
- 顧客管理
- 注文管理
- ログイン履歴管理

## 使用技術

### バックエンド
- PHP 8.3
- MariaDB 10.11.14

### フロントエンド
- HTML5
- CSS3
- JavaScript

### Webサーバー
- Apache 2.4.58

### 開発環境
- Ubuntu 24.04.3-live-server (VirtualBox)
- Composer
- Xdebug
- Visual Studio Code
- Git / GitHub

### 使用ライブラリ
- vlucas/phpdotenv

## セキュリティ

- XSS対策
- バリデーション
- CSRF対策
- ワンタイムトークン
- SQLインジェクション対策
- セッションID再生成
- 連続ログインブロック

## ディレクトリ構成

<details>
<summary>ディレクトリ構成</summary>
<pre>
/myproject/
.
├── composer.json
├── composer.lock
├── images
│   ├── 10.png
│   ├── 11.png
│   ├── 12.png
│   ├── 13.png
│   ├── 14.png
│   ├── 15.png
│   ├── 16.png
│   ├── 1.png
│   ├── 2.png
│   ├── 3.png
│   ├── 4.png
│   ├── 5.png
│   ├── 6.png
│   ├── 7.png
│   └── 8.png
├── public
│   ├── admin
│   │   ├── cancel_order
│   │   ├── err
│   │   ├── index.php
│   │   ├── login
│   │   ├── logout
│   │   ├── manage
│   │   │   ├── customer
│   │   │   ├── index.php
│   │   │   ├── pagecreate
│   │   │   ├── product
│   │   │   │   ├── create
│   │   │   │   ├── delete
│   │   │   │   ├── list.php
│   │   │   │   ├── read
│   │   │   │   └── update
│   │   │   ├── staff
│   │   │   │   ├── create
│   │   │   │   ├── delete
│   │   │   │   ├── list.php
│   │   │   │   ├── read
│   │   │   │   └── update
│   │   │   └── system
│   │   │       ├── admin_email
│   │   │       ├── admin_ip
│   │   │       ├── customer_email
│   │   │       ├── customer_ip
│   │   │       └── index.php
│   │   ├── order
│   │   ├── sales
│   │   └── timeout
│   ├── admin_header.php
│   ├── customer
│   │   ├── err
│   │   ├── his
│   │   │   ├── current
│   │   │   ├── index.php
│   │   │   └── past
│   │   ├── login
│   │   ├── logout
│   │   ├── register
│   │   ├── timeout
│   │   ├── top
│   │   │   ├── cart
│   │   │   ├── cart_in.php
│   │   │   ├── cart_out.php
│   │   │   ├── delivery
│   │   │   ├── index.php
│   │   │   └── top.js
│   │   └── view
│   │       ├── change_email
│   │       ├── change_pass
│   │       ├── edit
│   │       ├── index.php
│   │       └── withdraw
│   ├── customer_header.php
│   ├── footer.php
│   ├── header_footer.css
│   ├── img
│   │   ├── 1.png
│   │   ├── 2.png
│   │   ├── map.png
│   │   ├── piza01.png
│   │   ├── piza02.png
│   │   ├── piza03.png
│   │   ├── piza04.png
│   │   ├── piza05.png
│   │   ├── piza06.png
│   │   ├── pizalogo.png
│   │   └── sample.png
│   ├── index.php
│   ├── info
│   │   ├── privacy.html
│   │   └── usecookie.html
│   ├── initialsetting.css
│   ├── json
│   │   ├── dessert.json
│   │   └── meal.json
│   └── style.css
├── README.md
├── src
│   ├── Admin
│   │   ├── Service
│   │   │   ├── AdminCancelOrder.php
│   │   │   ├── AdminLogin.php
│   │   │   ├── AdminLogout.php
│   │   │   ├── AdminManageCustomer.php
│   │   │   ├── AdminManagePagecreate.php
│   │   │   ├── AdminManageProduct.php
│   │   │   ├── AdminManageStaff.php
│   │   │   ├── AdminManageSystem.php
│   │   │   ├── AdminOrder.php
│   │   │   ├── AdminSales.php
│   │   │   └── AdminTimeout.php
│   │   ├── Token
│   │   │   ├── AdminCsrf.php
│   │   │   └── AdminOneTimeToken.php
│   │   └── Validator
│   │       ├── AdminPostValidator.php
│   │       ├── AdminSessionValidator.php
│   │       └── Database
│   │           ├── AdminLoginEmailRepositoryValidator.php
│   │           ├── AdminLoginIpRepositoryValidator.php
│   │           ├── AdminRepositoryValidator.php
│   │           ├── CustomerLoginEmailRepositoryValidator.php
│   │           ├── CustomerLoginIpRepositoryValidator.php
│   │           ├── CustomerRepositoryValidator.php
│   │           ├── OrderRepositoryValidator.php
│   │           └── ProductRepositoryValidator.php
│   ├── Common
│   │   ├── DisplayDateWeek.php
│   │   ├── ErrLog.php
│   │   ├── Htmlspecialchars.php
│   │   ├── JsonResponse.php
│   │   ├── RedirectPage.php
│   │   └── RegexCheck.php
│   ├── Config
│   │   ├── Database.php
│   │   ├── Env.php
│   │   └── Path.php
│   ├── Constants
│   │   ├── AdminRole.php
│   │   ├── CustomerStatus.php
│   │   └── OrderStatus.php
│   ├── Customer
│   │   ├── Service
│   │   │   ├── CustomerHis.php
│   │   │   ├── CustomerLogin.php
│   │   │   ├── CustomerLogout.php
│   │   │   ├── CustomerRegister.php
│   │   │   ├── CustomerTimeout.php
│   │   │   ├── CustomerTop.php
│   │   │   └── CustomerView.php
│   │   ├── Token
│   │   │   ├── CustomerCsrf.php
│   │   │   └── CustomerOneTimeToken.php
│   │   └── Validator
│   │       ├── CustomerPostValidator.php
│   │       ├── CustomerSessionValidator.php
│   │       └── Database
│   │           ├── CustomerLoginEmailRepositoryValidator.php
│   │           ├── CustomerLoginIpRepositoryValidator.php
│   │           ├── CustomerRepositoryValidator.php
│   │           ├── OrderitemRepositoryValidator.php
│   │           ├── OrderRepositoryValidator.php
│   │           └── ProductRepositoryValidator.php
│   └── Repository
│       ├── AdminLoginEmailRepository.php
│       ├── AdminLoginIpRepository.php
│       ├── AdminRepository.php
│       ├── CustomerLoginEmailRepository.php
│       ├── CustomerLoginIpRepository.php
│       ├── CustomerRepository.php
│       ├── OrderitemRepository.php
│       ├── OrderRepository.php
│       └── ProductRepository.php
├── storage
│   └── trash
│       ├── img
│       └── json
└── vendor
</pre>
</details>

## 工夫した点

以下の3点は、特に意識して実装しました。<br>
<br>
１：Repository層とService層の分離<br>
２：セキュリティ対策<br>
３：Session設計<br>

### １：Repository層とService層の分離

Repository層とService層を分離し、<br>
それぞれの責務を明確にしました。<br>
<br>
・Repository層　＝データベースアクセスを担当<br>
・Service層　　　＝業務ロジックを担当<br>
<br>
責務を分けることでコードの見通しが良くなり、<br>
保守性や再利用性を高めています。<br>
<br>
設計については試行錯誤を重ね、<br>
現在の構成に至りました。<br>

### ２：セキュリティ対策
<br>
ECサイトとしての基本的なセキュリティを実装しました。<br>
<br>
１：XSS対策<br>
２：CSRF対策<br>
３：One-Time tokenによる二重送信防止<br>
<br>
４：SQLインジェクション対策<br>
５：連続ログイン制限<br>
<br>
特に連続ログイン制限では、<br>
ログイン失敗回数に応じて、<br>
待機時間が増加する仕組みを実装しました。<br>
<br>
待機時間は、<br>
（失敗回数 + 1）² 秒　で加算され、<br>
最大約50秒まで増加します。<br>
<br>
これにより、<br>
ブルートフォース攻撃（総当たり攻撃）への耐性を高めています。<br>
<br>
また、失敗回数などの情報は、<br>
データベースに保存しているため、<br>
<br>
将来的には特定のIPアドレスをログイン禁止にする機能<br>
なども追加できる設計にしています。<br>

### ３：Sessionの設計
<br>
Sessionは用途ごとにデータを分類し、<br>
管理しやすい構成にしました。<br>
<br>
＜例＞<br>

```php
$_SESSION['customer']['form']
$_SESSION['customer']['cart']
```

<br>
このように役割ごとに管理することで、<br>
必要なデータだけを取得・削除でき、<br>
<br>
可読性や保守性の向上につなげています。<br>

## 苦労した点

最も苦労したのは、LAMP環境の構築です。<br>
<br>
VirtualBox（Ubuntu 24.04.3）に<br>
「Apache」「PHP」「MariaDB」を導入し、<br>
開発環境を整えるまでに、多くの時間を要しました。<br>
<br>
また、<br>
開発中には、<br>
Apacheが「www-data」ユーザーで動作することを理解しておらず、<br>
ファイルの所有権や権限が原因で問題が発生しました。<br>
<br>
これらの経験を通して、<br>
Linuxの権限管理や開発環境の仕組みについて<br>
理解を深めることができました。<br>

## テーブル一覧

以下は、MariaDBで作成した「テーブルの一覧（簡易）」です。<br>
<br>
PK = PRIMARY KEY<br>
FK = FOREIGN KEY<br>
<br>
<pre>
管理者アカウント
+------------------+
| admins           |
+------------------+
| PK id            |
| email            |
| pass             |
+------------------+

顧客アカウント
+------------------+
| customers        |
+------------------+
| PK id            |
| email            |
| pass             |
+------------------+

商品
+------------------+
| products         |
+------------------+
| PK id            |
| name             |
| price            |
+------------------+

注文
+------------------+
| orders           |
+------------------+
| PK id            |
| FK customer_id   |
| status           |
+------------------+

注文明細
+------------------+
| order_items      |
+------------------+
| PK id            |
| FK order_id      |
| FK product_id    |
| quantity         |
+------------------+
</pre>

## ER図

以下は「ER図（簡易）」です。<br>
<br>
PK = PRIMARY KEY<br>
FK = FOREIGN KEY<br>
1N = 1:N（1対多数）<br>
<br>
customers = 顧客<br>
products = 商品<br>
<br>
orders = 注文<br>
order_items = 注文明細<br>

<pre>
+------------------+
| customers        |
+------------------+
| PK id            |
| name             |
| email            |
+------------------+
          │
          │1
          │
          │N
+------------------+
| orders           |
+------------------+
| PK id            |
| FK customer_id   |
| status           |
+------------------+
          │
          │1
          │
          │N
+------------------+
| order_items      |
+------------------+
| PK id            |
| FK order_id      |
| FK product_id    |
| quantity         |
+------------------+
          ▲
          │N
          │
          │1
+------------------+
| products         |
+------------------+
| PK id            |
| name             |
| price            |
+------------------+
</pre>

## 今後の学習予定

- PHPUnit
- Docker対応
- Laravel版の制作

## 学んだこと

このプロジェクトを通して、<br>
<br>
・PHPによるMVCを意識した設計<br>
・データベース設計<br>
・セキュリティ対策<br>
<br>
これらの理解を深め、学ぶことができました。<br>

## 注意

このサイトはポートフォリオ目的です。<br>
<br>
実際の商品販売は行っていません。<br>
<br>
もしこのコードを使用する場合、<br>
入力する個人情報は、架空のものをご使用ください。<br>

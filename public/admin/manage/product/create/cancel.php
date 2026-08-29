<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';

session_start();

unset($_SESSION['admin']['form']['product_create']);
unset($_SESSION['admin']['system']);

header('Location: /admin/manage/product/list.php');
exit();

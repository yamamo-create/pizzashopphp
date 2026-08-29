<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';

session_start();

unset($_SESSION['admin']['form']['product_update']);
unset($_SESSION['admin']['system']['choice_id']);

header('Location: /admin/manage/product/list.php');
exit();

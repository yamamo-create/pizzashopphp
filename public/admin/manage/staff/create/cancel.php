<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';

session_start();

unset($_SESSION['admin']['form']['create']);
unset($_SESSION['admin']['system']);

header('Location: /admin/manage/staff/list.php');
exit();

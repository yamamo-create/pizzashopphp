<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';

session_start();

unset($_SESSION['admin']['form']['page']);

header('Location: /admin/manage/index.php');
exit();

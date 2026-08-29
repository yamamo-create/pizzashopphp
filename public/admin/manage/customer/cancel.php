<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';

session_start();

unset($_SESSION['admin']['form']['customer']);

header('Location: /admin/manage/index.php');
exit();

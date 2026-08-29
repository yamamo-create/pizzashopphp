<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';

session_start();

unset($_SESSION['customer']['form']['change_pass']);

header('Location: /customer/view/index.php');
exit();

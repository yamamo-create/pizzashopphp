<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';

session_start();

unset($_SESSION['customer']['cart']);
unset($_SESSION['customer']['order']);

header('Location: /customer/top/index.php');
exit();

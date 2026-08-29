<?php
require_once __DIR__ . '/../../../../src/Config/Path.php';

session_start();

unset($_SESSION['customer']['cart']);

header('Location: /customer/top/index.php');
exit();

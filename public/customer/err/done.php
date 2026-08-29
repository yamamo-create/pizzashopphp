<?php
require_once __DIR__ . '/../../../src/Config/Path.php';

session_start();

unset($_SESSION['customer']['form']);
unset($_SESSION['customer']['order']);
unset($_SESSION['customer']['order_his']);
unset($_SESSION['customer']['one_time_token']);

header('Location: complete.php');
exit();

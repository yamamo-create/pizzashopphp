<?php
require_once __DIR__ . '/../../../src/Config/Path.php';

session_start();

unset($_SESSION['admin']['form']);
unset($_SESSION['admin']['login_history']);
unset($_SESSION['admin']['one_time_token']);

header('Location: complete.php');
exit();

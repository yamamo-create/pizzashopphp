<?php
require_once __DIR__ . '/../../../src/Config/Path.php';

session_start();

unset($_SESSION['customer']['form']);

header('Location: /index.php');
exit();

<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Customer/Service/CustomerTimeout.php';

use App\Customer\Service\CustomerTimeout;

session_start();

$customerTimeout = new CustomerTimeout();
$customerTimeout->logoutDueToTimeout();

header('Location: complete.php');
exit();

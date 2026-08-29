<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Customer/Service/CustomerLogout.php';

use App\Customer\Service\CustomerLogout;

session_start();

$customerLogout = new CustomerLogout();
$customerLogout->logout();

header('Location: complete.php');
exit();

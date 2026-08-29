<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Admin/Service/AdminTimeout.php';

use App\Admin\Service\AdminTimeout;

session_start();

$adminTimeout = new AdminTimeout();
$adminTimeout->logoutDueToTimeout();

header('Location: complete.php');
exit();

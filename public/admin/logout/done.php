<?php
require_once __DIR__ . '/../../../src/Config/Path.php';
require_once SRC_PATH . '/Admin/Service/AdminLogout.php';

use App\Admin\Service\AdminLogout;

session_start();

$adminLogout = new AdminLogout();
$adminLogout->logout();

header('Location: complete.php');
exit();

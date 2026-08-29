<?php
require_once __DIR__ . '/../../../../../src/Config/Path.php';

session_start();

header('Location: /admin/manage/staff/list.php');
exit();

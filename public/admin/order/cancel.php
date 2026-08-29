<?php
require_once __DIR__ . '/../../../src/Config/Path.php';

session_start();

header('Location: /admin/index.php');
exit();

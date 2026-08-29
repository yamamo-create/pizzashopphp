<?php

namespace App\Config;

require_once __DIR__ . '/Path.php';
require_once SRC_PATH . '/Config/Env.php';

use \PDO;

class Database
{
    public static function connect(): PDO
    {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';' . $_ENV['DB_CHARSET'];
        $dbh = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
        // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // PDO::ATTR_EMULATE_PREPARES   => false
        return $dbh;
    }
}

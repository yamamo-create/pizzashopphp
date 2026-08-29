<?php

namespace App\Repository;

require_once __DIR__ . '/../Config/Path.php';
require_once SRC_PATH . '/Config/Database.php';

use App\Config\Database;

use PDO;
use PDOException;
use RuntimeException;

class AdminLoginEmailRepository
{
    private string $currentTime;

    public function __construct()
    {
        $this->currentTime = date('Y-m-d H:i:s', time());
    }

    //
    // ----- [CREATE] read update delete -----
    //
    public function insert(string $email, bool $success, int $fail_count): void
    {
        try {
            $dbh = Database::connect();
            $sql = "INSERT INTO admin_login_fail_emails(
                email,
                ip,
                created_at,
                success,
                fail_count
            ) 
            VALUES(
                :email,
                :ip,
                :time,
                :success,
                :fail_count
            )";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
            $stmt->bindValue(':time', $this->currentTime, PDO::PARAM_STR);
            $stmt->bindValue(':success', $success, PDO::PARAM_BOOL);
            $stmt->bindValue(':fail_count', $fail_count, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }

    // 
    // ----- create [READ] update delete -----
    //
    public function findAll(): array
    {
        try {
            $dbh = Database::connect();

            $sql = "SELECT 
                id,
                email,
                ip,
                created_at,
                success,
                fail_count
            FROM 
                admin_login_fail_emails 
            ORDER BY created_at DESC 
            LIMIT 100
            ";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findOne(string $email): false|array
    {
        try {
            $dbh = Database::connect();

            $sql = "SELECT 
                id,
                email,
                ip,
                created_at,
                success,
                fail_count 
            FROM admin_login_fail_emails 
            WHERE email=:email
            ORDER BY created_at DESC 
            LIMIT 1
            ";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }
    // 
    // ----- create read [UPDATE] delete -----
    // なし

    // 
    // ----- create read update [DELETE] -----
    // 
    public function deleteAll(): void
    {
        try {
            $dbh = Database::connect();

            $sql = 'DELETE FROM admin_login_fail_emails';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }
}

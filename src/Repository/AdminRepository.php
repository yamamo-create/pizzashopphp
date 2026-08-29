<?php

namespace App\Repository;

require_once __DIR__ . '/../Config/Path.php';
require_once SRC_PATH . '/Config/Database.php';

use App\Config\Database;

use PDO;
use PDOException;
use RuntimeException;

class AdminRepository
{
    private string $currentTime;

    public function __construct()
    {
        $this->currentTime = date('Y-m-d H:i:s', time());
    }

    // 
    // ----- [CREATE] read update delete -----
    // 
    public function insert(array $adminData): false|int
    {
        $auth = $adminData['auth'];
        $email = $adminData['email'];
        $password = $adminData['password'];

        $passHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $dbh = Database::connect();
            $sql = "INSERT INTO admins
            (auth,email,password,created_at) 
            VALUES
            (:auth,:email,:password,:created_at)";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':auth', $auth, PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $passHash, PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $this->currentTime, PDO::PARAM_STR);
            $stmt->execute();
            $insertAdminId = (int)$dbh->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $insertAdminId;
    }

    // 
    // ----- create [READ] update delete -----
    // 
    public function findAll(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT id,auth,email,created_at,deleted_at 
            FROM admins 
            WHERE deleted_at IS NULL 
            LIMIT 1000";

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

    public function findOne(int $id): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
                id,
                auth,
                email,
                created_at,
                deleted_at 
            FROM 
                admins 
            WHERE 
                id=:id";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException("adminが見つかりません。(id={$id})");
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function existsByEmail(string $email): bool
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 1 FROM admins WHERE email=:email LIMIT 1";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec !== false;
    }

    public function findByEmail(string $email): false|array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT id,auth,password FROM admins WHERE email=:email";
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
    // 
    public function updateSuperPassword(string $password): void
    {
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $dbh = Database::connect();
            $sql = "UPDATE admins 
            SET 
                password=:password
            WHERE 
                auth=9 
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':password', $passHash, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }

    // 
    // ----- create read update [DELETE] -----
    // 
    public function delete(int $id, string $email): void
    {
        $deletedEmail = 'deleted_' . uniqid('', true);

        try {
            $dbh = Database::connect();
            $sql = "UPDATE admins 
            SET 
                auth=0,
                email=:deleted_email,
                password='0',
                deleted_at=:time 
            WHERE id=:id AND email=:email
            ";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':time', $this->currentTime, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':deleted_email', $deletedEmail, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }
}

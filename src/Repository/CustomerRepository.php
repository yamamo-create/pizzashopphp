<?php

namespace App\Repository;

require_once __DIR__ . '/../Config/Path.php';
require_once SRC_PATH . '/Config/Database.php';

use App\Config\Database;

use PDO;
use PDOException;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class CustomerRepository
{
    private string $currentTime;

    public function __construct()
    {
        $this->currentTime = date('Y-m-d H:i:s', time());
    }

    // 
    // ----- [CREATE] read update delete -----
    // 
    public function insert(array $customerData): int
    {
        try {
            $email = $customerData['email'] ?? null;
            $password = $customerData['password'] ?? null;
            $lastname = $customerData['lastname'] ?? null;
            $firstname = $customerData['firstname'] ?? null;
            $phone = $customerData['phone'] ?? null;
            $post = $customerData['post'] ?? null;
            $address = $customerData['address'] ?? null;

            if (
                !is_string($email) ||
                !is_string($password) ||
                !is_string($lastname) ||
                !is_string($firstname) ||
                !is_string($phone) ||
                !is_string($post) ||
                !is_string($address)
            ) {
                throw new InvalidArgumentException('引数が間違っています');
            }

            $passHash = password_hash($password, PASSWORD_DEFAULT);

            $dbh = Database::connect();
            $sql = "INSERT INTO customers
            (email,password,lastname,firstname,phone,post,address,created_at) 
            VALUES
            (:email,:password,:lastname,:firstname,:phone,:post,:address,:created_at)";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $passHash, PDO::PARAM_STR);
            $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindValue(':post', $post, PDO::PARAM_STR);
            $stmt->bindValue(':address', $address, PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $this->currentTime, PDO::PARAM_STR);
            $stmt->execute();
            $insertCustomerId = (int)$dbh->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $insertCustomerId;
    }

    // 
    // ----- create [READ] update delete -----
    // 
    public function findAll(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT * FROM customers WHERE deleted_at IS NULL";
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

    public function findOne(int $id, string $email): false|array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
                id,
                email,
                lastname,
                firstname,
                phone,
                post,
                address,
                status,
                created_at,
                updated_at,
                deleted_at 
            FROM 
                customers 
            WHERE 
                id=:id AND email=:email";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
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

    public function existsByEmail(string $email): bool
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 1 FROM customers WHERE email=:email LIMIT 1";
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
            $sql = "SELECT id,password,status FROM customers WHERE email=:email";
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

    public function findOneId(int $id): false|array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
                id,
                email,
                lastname,
                firstname,
                phone,
                post,
                address,
                status,
                created_at,
                updated_at,
                deleted_at  
            FROM 
                customers 
            WHERE 
                id=:id";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
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

    //苗字、名前、電話番号、郵便番号、住所の変更
    public function updateAccount(array $customerData): void
    {
        try {
            $id        = $customerData['id'] ?? null;
            $email     = $customerData['email'] ?? null;
            $lastname  = $customerData['lastname'] ?? null;
            $firstname = $customerData['firstname'] ?? null;
            $phone     = $customerData['phone'] ?? null;
            $post      = $customerData['post'] ?? null;
            $address   = $customerData['address'] ?? null;

            if (
                !ctype_digit(strval($id)) ||
                !is_string($email) ||
                !is_string($lastname) ||
                !is_string($firstname) ||
                !is_string($phone) ||
                !is_string($post) ||
                !is_string($address)
            ) {
                throw new InvalidArgumentException('引数が間違っています');
            }

            $dbh = Database::connect();
            $sql = "UPDATE customers 
            SET 
                lastname=:lastname,
                firstname=:firstname,
                phone=:phone,
                post=:post,
                address=:address 
            WHERE 
                id=:id AND email=:email
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindValue(':post', $post, PDO::PARAM_STR);
            $stmt->bindValue(':address', $address, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }

    //メールアドレスの変更
    public function updateEmail(int $id, string $email, string $changeEmail): void
    {
        try {
            $dbh = Database::connect();
            $sql = "UPDATE customers 
            SET 
                email=:changeEmail 
            WHERE 
                id=:id AND email=:email
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':changeEmail', $changeEmail, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }

    //パスワードの変更
    public function updatePassword(int $id, string $email, string $changePassword): void
    {
        $passHash = password_hash($changePassword, PASSWORD_DEFAULT);

        try {
            $dbh = Database::connect();
            $sql = "UPDATE customers 
            SET 
                password=:passHash 
            WHERE 
                id=:id AND email=:email
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':passHash', $passHash, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }

    public function updateStatus(int $id, int $status): void
    {
        try {
            $dbh = Database::connect();
            $sql = "UPDATE customers 
            SET 
                status=:status 
            WHERE 
                id=:id 
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
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
            $sql = "UPDATE customers 
            SET 
                email=:deleted_email,
                password='[deleted]',
                lastname='[deleted]',
                firstname='[deleted]',
                phone='[deleted]',
                post='[deleted]',
                address='[deleted]',
                deleted_at=:time 
            WHERE id=:id AND email=:email";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':deleted_email', $deletedEmail, PDO::PARAM_STR);
            $stmt->bindValue(':time', $this->currentTime, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }
}

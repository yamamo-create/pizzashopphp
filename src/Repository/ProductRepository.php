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

class ProductRepository
{
    private string $currentTime;

    public function __construct()
    {
        $this->currentTime = date('Y-m-d H:i:s', time());
    }

    // 
    // ----- [CREATE] read update delete -----
    // 
    public function insert(array $productData): int
    {
        $name = $productData['name'] ?? null;
        $price = $productData['price'] ?? null;
        $detail = $productData['detail'] ?? null;

        $imagename = $productData['imagename'] ?? null;
        $tmpName = $productData['tmp_name'] ?? null;
        $destination = $productData['destination'] ?? null;

        if (
            is_null($name) ||
            is_null($price) ||
            is_null($detail) ||
            is_null($imagename) ||
            is_null($tmpName) ||
            is_null($destination)
        ) {
            throw new RuntimeException('商品データ不正');
        }

        try {
            $dbh = Database::connect();
            $dbh->beginTransaction();
            $sql = "INSERT INTO products
            (name,price,imagename,detail) 
            VALUES
            (:name,:price,:imagename,:detail)";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price, PDO::PARAM_INT);
            $stmt->bindValue(':imagename', $imagename, PDO::PARAM_STR);
            $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
            $stmt->execute();
            $insertproductId = (int)$dbh->lastInsertId();

            if (!move_uploaded_file($tmpName, $destination)) {
                throw new RuntimeException('画像保存失敗');
            }
            $dbh->commit();
            return $insertproductId;
        } catch (Throwable $e) {
            if (isset($dbh) && $dbh->inTransaction()) {
                $dbh->rollBack();
            }
            if (isset($destination) && file_exists($destination)) {
                unlink($destination);
            }
            throw new RuntimeException('Database issue', 0, $e);
        }
    }
    // 
    // ----- create [READ] update delete -----
    // 
    public function findAll(): array|false
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT id,name,price,imagename,detail,created_at,updated_at,deleted_at 
            FROM products 
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
                id,name,price,imagename,detail,created_at,updated_at,deleted_at 
            FROM 
                products 
            WHERE 
                id=:id";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new InvalidArgumentException('$idに対するProductデータが存在しない');
            }
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
    public function update(array $productData): void
    {
        $id = $productData['id'] ?? null;
        $name = $productData['name'] ?? null;
        $price = $productData['price'] ?? null;
        $imagename = $productData['imagename'] ?? null;
        $detail = $productData['detail'] ?? null;

        $tmpName = $productData['tmp_name'] ?? null;

        $newDestination = $productData['new_destination'] ?? null;
        $oldDestination = $productData['old_destination'] ?? null;

        if (
            is_null($id) ||
            is_null($name) ||
            is_null($price) ||
            is_null($imagename) ||
            is_null($detail) ||
            is_null($tmpName) ||
            is_null($newDestination) ||
            is_null($oldDestination)
        ) {
            throw new RuntimeException('商品データ不正');
        }

        try {
            $dbh = Database::connect();
            $dbh->beginTransaction();
            $sql = "UPDATE products 
            SET 
                name=:name,
                price=:price,
                imagename=:imagename,
                detail=:detail
            WHERE 
                id=:id 
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price, PDO::PARAM_INT);
            $stmt->bindValue(':imagename', $imagename, PDO::PARAM_STR);
            $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if (!move_uploaded_file($tmpName, $newDestination)) {
                throw new RuntimeException('画像保存失敗');
            }
            $dbh->commit();

            if (isset($oldDestination) && file_exists($oldDestination)) {
                if (!rename(
                    $oldDestination,
                    TRASH_IMAGE_PATH . '/' . basename($oldDestination)
                )) {
                    error_log('旧画像移動失敗');
                }
            }
        } catch (PDOException $e) {
            if (isset($dbh) && $dbh->inTransaction()) {
                $dbh->rollBack();
            }
            if (isset($newDestination) && file_exists($newDestination)) {
                unlink($newDestination);
            }
            throw new RuntimeException('Database issue');
        }
    }

    // 
    // ----- create read update [DELETE] -----
    // 
    public function delete(int $id, string $destination): void
    {
        try {
            $dbh = Database::connect();
            $dbh->beginTransaction();
            $sql = "UPDATE products 
            SET 
                deleted_at=:time 
            WHERE id=:id
            ";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':time', $this->currentTime, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if (file_exists($destination) && !rename(
                $destination,
                TRASH_IMAGE_PATH . '/' . basename($destination)
            )) {
                throw new RuntimeException('画像移動失敗');
            }

            $dbh->commit();
        } catch (Throwable $e) {
            if (isset($dbh) && $dbh->inTransaction()) {
                $dbh->rollBack();
            }
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }
}

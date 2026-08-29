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

class OrderitemRepository
{
    private string $currentTime;

    public function __construct()
    {
        $this->currentTime = date('Y-m-d H:i:s', time());
    }

    // 
    // ----- [CREATE] read update delete -----
    // 
    public function insert(
        PDO $dbh,
        int $order_id,
        int $product_id,
        string $product_name,
        int $product_price,
        int $product_quantity
    ): void {

        try {
            $sql = "INSERT INTO order_items
            (order_id,product_id,product_name,product_price,product_quantity) 
            VALUES
            (:order_id,:product_id,:product_name,:product_price,:product_quantity)";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindValue(':product_name', $product_name, PDO::PARAM_STR);
            $stmt->bindValue(':product_price', $product_price, PDO::PARAM_INT);
            $stmt->bindValue(':product_quantity', $product_quantity, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        }
    }

    // 
    // ----- create [READ] update delete -----
    // 
    public function findOne(int $id): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            id,
            order_id,
            product_id,
            product_name,
            product_price,
            product_quantity,
            created_at,
            deleted_at 
            FROM order_items 
            WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('注文履歴データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findAllOrderId(int $orderId): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            id,
            order_id,
            product_id,
            product_name,
            product_price,
            product_quantity,
            created_at,
            deleted_at 
            FROM order_items 
            WHERE order_id = :order_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === []) {
                throw new RuntimeException('データを受信できません');
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

    // 
    // ----- create read update [DELETE] -----
    // 

}

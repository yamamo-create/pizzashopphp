<?php

namespace App\Repository;

require_once __DIR__ . '/../Config/Path.php';
require_once SRC_PATH . '/Config/Database.php';

use App\Config\Database;

use PDO;
use PDOException;
use Throwable;
use RuntimeException;

class OrderRepository
{
    private string $currentTime;

    public function __construct()
    {
        $this->currentTime = date('Y-m-d H:i:s', time());
    }

    // 
    // ----- [CREATE] read update delete -----
    // 
    public function insert(PDO $dbh, int $customer_id, int $status, int $total_price): int
    {
        try {
            $sql = "INSERT INTO orders
            (customer_id,status,total_price) 
            VALUES
            (:customer_id,:status,:total_price)";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->bindValue(':total_price', $total_price, PDO::PARAM_INT);
            $stmt->execute();
            $orderId = (int)$dbh->lastInsertId();
            if ($orderId <= 0) {
                throw new RuntimeException('注文テーブルで作成された自動IDを返せませんでした');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        }
        return $orderId;
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
            customer_id,
            status,
            total_price,
            created_at,
            updated_at,
            completed_at 
            FROM orders 
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

    public function findCurrentCustomerOrder(int $customer_id): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id,
            o.status,
            o.total_price,
            o.created_at,
            oi.product_name,
            oi.product_price,
            oi.product_quantity 
            FROM orders o 
            INNER JOIN 
            order_items oi 
            ON o.id = oi.order_id 
            WHERE o.customer_id = :customer_id 
            AND o.status IN (1,2,3) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findCompletCustomerOrder(int $customer_id): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id,
            o.status,
            o.total_price,
            o.created_at,
            oi.product_name,
            oi.product_price,
            oi.product_quantity 
            FROM orders o 
            INNER JOIN 
            order_items oi 
            ON o.id = oi.order_id 
            WHERE o.customer_id = :customer_id 
            AND o.status IN (4) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findOrderPlusCustomer(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id AS order_id,
            o.customer_id,
            o.status,
            o.total_price,
            o.created_at,
            c.id,
            c.email,
            c.lastname,
            c.firstname,
            c.phone,
            c.post,
            c.address             
            FROM orders o 
            INNER JOIN customers c ON o.customer_id = c.id       
            WHERE o.status IN (1,2,3) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('未完の注文データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findOrderPlusOrderitem(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id AS order_id,
            o.customer_id,
            o.status,
            o.total_price,
            o.created_at,
            oi.product_name,
            oi.product_price,
            oi.product_quantity 
            FROM orders o 
            INNER JOIN order_items oi ON o.id = oi.order_id       
            WHERE o.status IN (1,2,3) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('未完の注文データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findCompleteOrderPlusCustomer(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id AS order_id,
            o.customer_id,
            o.status,
            o.total_price,
            o.created_at,
            c.id,
            c.email,
            c.lastname,
            c.firstname,
            c.phone,
            c.post,
            c.address             
            FROM orders o 
            INNER JOIN customers c ON o.customer_id = c.id       
            WHERE o.status IN (4) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('注文データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findCompleteOrderPlusOrderitem(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id AS order_id,
            o.customer_id,
            o.status,
            o.total_price,
            o.created_at,
            oi.product_name,
            oi.product_price,
            oi.product_quantity 
            FROM orders o 
            INNER JOIN order_items oi ON o.id = oi.order_id       
            WHERE o.status IN (4) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('販売履歴データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findCancelOrderPlusCustomer(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id AS order_id,
            o.customer_id,
            o.status,
            o.total_price,
            o.created_at,
            c.id,
            c.email,
            c.lastname,
            c.firstname,
            c.phone,
            c.post,
            c.address             
            FROM orders o 
            INNER JOIN customers c ON o.customer_id = c.id       
            WHERE o.status IN (9) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('注文データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findCancelOrderPlusOrderitem(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT 
            o.id AS order_id,
            o.customer_id,
            o.status,
            o.total_price,
            o.created_at,
            oi.product_name,
            oi.product_price,
            oi.product_quantity 
            FROM orders o 
            INNER JOIN order_items oi ON o.id = oi.order_id       
            WHERE o.status IN (9) 
            ORDER BY o.created_at ASC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('販売履歴データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    // ----- もしも使うなら（現在未使用） -----

    public function findSalesByDay(): array
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT
            DATE(completed_at) AS day,
            SUM(total_price) AS sales
            FROM
            orders
            WHERE
            status = 4
            GROUP BY
            DATE(completed_at)
            ORDER BY day DESC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('本日の売上データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findSalesByMonth(): array|false
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT
            DATE_FORMAT(completed_at,'%Y-%m') AS month,
            SUM(total_price) AS sales
            FROM
            orders
            WHERE
            status = 4
            GROUP BY
            month
            ORDER BY
            month DESC";

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rec === false) {
                throw new RuntimeException('月別売上データを受信できません');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
        return $rec;
    }

    public function findProductCount(): array|false
    {
        try {
            $dbh = Database::connect();
            $sql = "SELECT
            product_id,
            COUNT(*) AS cnt
            FROM order_items
            GROUP BY product_id;";

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

    //
    // ----- create read [UPDATE] delete -----
    // 
    public function changeStatus(int $id, int $status): void
    {
        try {
            $dbh = Database::connect();
            $sql = "UPDATE orders 
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

    public function completedStatus(int $id): void
    {
        try {
            $dbh = Database::connect();
            $sql = "UPDATE orders 
            SET 
                status=4,
                completed_at=:time 
            WHERE 
                id=:id 
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':completed_at', $this->currentTime, PDO::PARAM_STR);
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
    public function delete(int $id): void
    {
        try {
            $dbh = Database::connect();
            $sql = "UPDATE orders 
            SET 
                status=0 
            WHERE id=:id
            ";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException('Database issue', 0, $e);
        } finally {
            $dbh = null;
        }
    }
}

<?php

namespace App\Customer\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Database.php';
require_once SRC_PATH . '/Constants/OrderStatus.php';
require_once SRC_PATH . '/Customer/Validator/CustomerPostValidator.php';
require_once SRC_PATH . '/Repository/CustomerRepository.php';
require_once SRC_PATH . '/Repository/ProductRepository.php';
require_once SRC_PATH . '/Repository/OrderRepository.php';
require_once SRC_PATH . '/Repository/OrderitemRepository.php';
require_once SRC_PATH . '/Customer/Validator/Database/CustomerRepositoryValidator.php';
require_once SRC_PATH . '/Customer/Validator/Database/ProductRepositoryValidator.php';
require_once SRC_PATH . '/Customer/Validator/Database/OrderRepositoryValidator.php';
require_once SRC_PATH . '/Customer/Validator/Database/OrderitemRepositoryValidator.php';

use App\Config\Database;
use App\Constants\OrderStatus;
use App\Customer\Validator\CustomerPostValidator;

use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderitemRepository;
use App\Customer\Validator\Database\CustomerRepositoryValidator;
use App\Customer\Validator\Database\ProductRepositoryValidator;
use App\Customer\Validator\Database\OrderRepositoryValidator;
use App\Customer\Validator\Database\OrderitemRepositoryValidator;

use PDOException;
use RuntimeException;

class CustomerTop
{
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;
    private OrderRepository $orderRepository;
    private OrderitemRepository $orderitemRepository;
    private CustomerPostValidator $customerPostValidator;

    private CustomerRepositoryValidator $customerRepositoryValidator;
    private ProductRepositoryValidator $productRepositoryValidator;
    private OrderRepositoryValidator $orderRepositoryValidator;
    private OrderitemRepositoryValidator $orderitemRepositoryValidator;

    public function __construct()
    {
        $this->customerRepository = new CustomerRepository();
        $this->productRepository = new ProductRepository();
        $this->orderRepository = new OrderRepository();
        $this->orderitemRepository = new OrderitemRepository();
        $this->customerPostValidator = new CustomerPostValidator();

        $this->customerRepositoryValidator = new CustomerRepositoryValidator();
        $this->productRepositoryValidator = new ProductRepositoryValidator();
        $this->orderRepositoryValidator = new OrderRepositoryValidator();
        $this->orderitemRepositoryValidator = new OrderitemRepositoryValidator();
    }

    // ----- customer cart_in -----
    public function addProduct(array $post): void
    {
        $this->customerPostValidator->validatePostCustomerTopCart($post);

        $productId = intval($post['product_id']);

        $_SESSION['customer']['cart'] = $_SESSION['customer']['cart'] ?? [];
        $_SESSION['customer']['cart'][$productId]['quantity'] = 1;
    }

    // ----- customer cart_out -----
    public function removeProduct(array $post): void
    {
        $this->customerPostValidator->validatePostCustomerTopCart($post);

        $productId = intval($post['product_id']);

        if (isset($_SESSION['customer']['cart'][$productId])) {
            unset($_SESSION['customer']['cart'][$productId]);
        }
    }

    // ----- customer cart/cart_quantity -----
    public function changeProductQuantity(array $post): void
    {
        $this->customerPostValidator->validatePostCustomerTopCartCartQuantity($post);
        $productId = intval($post['product_id']);
        $quantity = intval($post['product_quantity']);

        $_SESSION['customer']['cart'][$productId]['quantity'] = $quantity;
    }

    // ----- customer cart/cart_delete -----
    public function deleteProduct(array $post): void
    {
        $this->customerPostValidator->validatePostCustomerTopCart($post);

        $productId = intval($post['product_id']);

        unset($_SESSION['customer']['cart'][$productId]);
    }

    // ----- customer cart/enter -----
    public function getCartProductAllData(array $cart): array
    {
        $productAllData = [];

        foreach ($cart as $productId => $item) {
            $productData = $this->productRepository->findOne($productId);
            $this->productRepositoryValidator->validateProductRepositoryFindOne($productData);

            $productData['quantity'] = $item['quantity'];
            $productAllData[] = $productData;
        }
        return $productAllData;
    }

    // ----- customer cart/confirm -----
    public function getCartTotalPrice(array $productAllData): int
    {
        $totalPrice = 0;

        foreach ($productAllData as $productData) {
            $totalPrice += intval($productData['price'] * $productData['quantity']);
        }
        return $totalPrice;
    }

    // ----- customer delivery/enter -----
    public function getCustomerDetail(int $id, string $email): array
    {
        $customerData = $this->customerRepository->findOne($id, $email);
        $this->customerRepositoryValidator->validateCustomerRepositoryFindOne($customerData);
        return $customerData;
    }

    // ----- customer delivery/done -----
    public function getOrderInfo(int $customerId, int $totalPrice): array
    {
        return [
            'customer_id' => $customerId,
            'status' => OrderStatus::RECEIVED,
            'total_price' => $totalPrice
        ];
    }
    public function getOrderitemInfo(array $cart): array
    {
        $orderitemInfo = [];

        foreach ($cart as $productId => $item) {
            $productData = $this->productRepository->findOne($productId);
            $this->productRepositoryValidator->validateProductRepositoryFindOne($productData);
            $productData['product_id'] = $productId;
            $productData['quantity'] = $item['quantity'];
            $orderitemInfo[] = $productData;
        }
        return $orderitemInfo;
    }
    public function createOrder(array $orderData, array $orderitemData): int
    {
        $orderId = null;

        try {
            $dbh = Database::connect();
            $dbh->beginTransaction();

            $customer_id = $orderData['customer_id'] ?? null;
            $status = $orderData['status'] ?? null;
            $total_price = $orderData['total_price'] ?? null;

            $orderId = $this->orderRepository->insert($dbh, $customer_id, $status, $total_price);

            foreach ($orderitemData as $value) {
                $product_id = $value['product_id'] ?? null;
                $product_name = $value['name'] ?? null;
                $product_price = $value['price'] ?? null;
                $product_quantity = $value['quantity'] ?? null;

                $this->orderitemRepository->insert(
                    $dbh,
                    $orderId,
                    $product_id,
                    $product_name,
                    $product_price,
                    $product_quantity
                );
            }
            $dbh->commit();
        } catch (PDOException $e) {
            if (isset($dbh) && $dbh->inTransaction()) {
                $dbh->rollBack();
            }
            throw new RuntimeException('Database issue', 0, $e);
        }
        return $orderId;
    }

    // ----- customer delivery/complete -----
    public function getOrderData(int $orderId): array
    {
        $orderData = $this->orderRepository->findOne($orderId);
        $this->orderRepositoryValidator->validateOrderRepositoryFindOne($orderData);
        return $orderData;
    }

    public function getOrderitemData(int $orderId): array
    {
        $orderitemData = [];

        $orderitemData = $this->orderitemRepository->findAllOrderId($orderId);

        foreach ($orderitemData as $value) {
            $this->orderitemRepositoryValidator->validateOrderitemRepositoryFindAllOrderId($value);
        }
        return $orderitemData;
    }

    public function getCustomerData(int $id, string $email): array
    {
        $customerData = $this->customerRepository->findOne($id, $email);
        $this->customerRepositoryValidator->validateCustomerRepositoryFindOne($customerData);
        return $customerData;
    }
}

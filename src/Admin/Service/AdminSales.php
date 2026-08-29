<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Admin/Validator/AdminPostValidator.php';

require_once SRC_PATH . '/Repository/OrderRepository.php';
require_once SRC_PATH . '/Admin/Validator/Database/OrderRepositoryValidator.php';

use InvalidArgumentException;

use App\Admin\Validator\AdminPostValidator;
use App\Repository\OrderRepository;
use App\Admin\Validator\Database\OrderRepositoryValidator;


// ----- admin sales -----
class AdminSales
{
    private bool $errorFlag;

    private array $errorMessage;

    private AdminPostValidator $adminPostValidator;

    private OrderRepository $orderRepository;
    private OrderRepositoryValidator $orderRepositoryValidator;

    public function __construct()
    {
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->adminPostValidator = new AdminPostValidator();

        $this->orderRepository = new OrderRepository();
        $this->orderRepositoryValidator = new orderRepositoryValidator;
    }

    // ----- admin sales enter.php -----
    public function getCompleteOrder(): array
    {
        $result = [];

        $orderData = $this->orderRepository->findCompleteOrderPlusCustomer();
        $this->orderRepositoryValidator->validateOrderRepositoryFindCompleteOrderPlusCustomer($orderData);

        $ordeitemrData = $this->orderRepository->findCompleteOrderPlusOrderitem();
        $this->orderRepositoryValidator->validateOrderRepositoryFindCompleteOrderPlusOrderitem($ordeitemrData);

        foreach ($orderData as $data) {
            $orderId = $data['order_id'] ?? '';
            $result[$orderId] = $data;
        }

        foreach ($ordeitemrData as $data) {
            $orderId =  $data['order_id'] ?? '';

            if (!isset($result[$orderId])) {
                throw new InvalidArgumentException('「order_itemのorder_id」に対する「ordersのorder_id」が存在しません');
            }
            $result[$orderId]['item'][] = $data;
        }
        return $result;
    }

    // ----- admin sales check_done.php -----
    public function validatePost(array $post): void
    {
        $this->adminPostValidator->validatePostAdminSalesCheck($post);
    }

    public function updateStatus(int $choice_id, int $orderStatus): void
    {
        $this->orderRepository->changeStatus($choice_id, $orderStatus);
    }

    // ----- 共通 -----
    public function geterrorFlag(): bool
    {
        return $this->errorFlag;
    }
    public function geterrorMessage(): array
    {
        return $this->errorMessage;
    }
}

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


// ----- admin canceled_orders -----
class AdminCancelOrder
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
        $this->orderRepositoryValidator = new OrderRepositoryValidator();
    }

    // ----- admin canceled_orders enter.php -----
    public function getCancelOrder(): array
    {
        $result = [];

        $orderData = $this->orderRepository->findCancelOrderPlusCustomer();
        $this->orderRepositoryValidator->validateOrderRepositoryFindCancelOrderPlusCustomer($orderData);

        $ordeitemrData = $this->orderRepository->findCancelOrderPlusOrderitem();
        $this->orderRepositoryValidator->validateOrderRepositoryFindCancelOrderPlusOrderitem($ordeitemrData);

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

    // ----- admin canceled_orders check_done.php -----
    public function validatePost(array $post): void
    {
        $this->adminPostValidator->validatePostAdminCancelOrder($post);
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

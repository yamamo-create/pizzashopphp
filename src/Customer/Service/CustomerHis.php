<?php

namespace App\Customer\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Constants/OrderStatus.php';
require_once SRC_PATH . '/Repository/OrderRepository.php';
require_once SRC_PATH . '/Customer/Validator/Database/OrderRepositoryValidator.php';

use App\Constants\OrderStatus;
use App\Repository\OrderRepository;
use App\Customer\Validator\Database\OrderRepositoryValidator;

class CustomerHis
{
    private OrderRepository $orderRepository;
    private OrderRepositoryValidator $orderRepositoryValidator;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->orderRepositoryValidator = new OrderRepositoryValidator();
    }

    // ----- customer his current -----
    public function getCurrentCustomerOrder(int $customerId): array
    {
        $orderData = [];
        $CurrentCustomerOrderData = $this->orderRepository->findCurrentCustomerOrder($customerId);
        if (empty($CurrentCustomerOrderData)) {
            return [];
        }
        $this->orderRepositoryValidator->validateOrderRepositoryFindCurrentCustomerOrder($CurrentCustomerOrderData);

        foreach ($CurrentCustomerOrderData as $data) {
            $orderId = $data['id'];
            $orderData[$orderId]['status'] = OrderStatus::LABELS[$data['status']];
            $orderData[$orderId]['total_price'] = $data['total_price'];
            $orderData[$orderId]['created_at'] = $data['created_at'];

            $orderData[$orderId]['item'][] = [
                'product_name' => $data['product_name'],
                'product_price' => $data['product_price'],
                'product_quantity' => $data['product_quantity'],
            ];
        }
        return $orderData;
    }

    // ----- customer his past -----
    public function getCompletedCustomerOrder(int $customerId): array
    {
        $orderData = [];
        $CurrentCustomerOrderData = $this->orderRepository->findCompletCustomerOrder($customerId);
        if (empty($CurrentCustomerOrderData)) {
            return [];
        }
        $this->orderRepositoryValidator->validateOrderRepositoryFindCompletedCustomerOrder($CurrentCustomerOrderData);

        foreach ($CurrentCustomerOrderData as $data) {
            $orderId = $data['id'];
            $orderData[$orderId]['status'] = OrderStatus::LABELS[$data['status']];
            $orderData[$orderId]['total_price'] = $data['total_price'];
            $orderData[$orderId]['created_at'] = $data['created_at'];

            $orderData[$orderId]['item'][] = [
                'product_name' => $data['product_name'],
                'product_price' => $data['product_price'],
                'product_quantity' => $data['product_quantity'],
            ];
        }
        return $orderData;
    }
}

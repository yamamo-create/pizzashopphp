<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Admin/Validator/AdminPostValidator.php';
require_once SRC_PATH . '/Repository/CustomerRepository.php';
require_once SRC_PATH . '/Repository/OrderRepository.php';
require_once SRC_PATH . '/Admin/Validator/Database/CustomerRepositoryValidator.php';
require_once SRC_PATH . '/Admin/Validator/Database/OrderRepositoryValidator.php';

use App\Admin\Validator\AdminPostValidator;

use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Admin\Validator\Database\CustomerRepositoryValidator;
use App\Admin\Validator\Database\OrderRepositoryValidator;

// ----- admin manage customer -----
class AdminManageCustomer
{
    private bool $errorFlag;

    private array $errorMessage;

    private AdminPostValidator $adminPostValidator;

    private CustomerRepository $customerRepository;
    private OrderRepository $orderRepository;
    private CustomerRepositoryValidator $customerRepositoryValidator;
    private OrderRepositoryValidator $orderRepositoryValidator;

    public function __construct()
    {
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->adminPostValidator = new AdminPostValidator();

        $this->customerRepository = new CustomerRepository();
        $this->orderRepository = new OrderRepository();
        $this->customerRepositoryValidator = new CustomerRepositoryValidator();
        $this->orderRepositoryValidator = new OrderRepositoryValidator();
    }

    // ----- admin manage customer enter.php -----
    public function getCustomerAllData(): array
    {
        $customerAllData = $this->customerRepository->findAll();
        $this->customerRepositoryValidator->validateCustomerRepositoryFindAll($customerAllData);
        return $customerAllData;
    }

    // ----- admin manage customer check.php -----
    public function validatePostCheck(array $post): void
    {
        $this->adminPostValidator->validatePostAdminManageCustomerCheck($post);
    }

    // ----- admin manage customer confirm.php -----
    public function getCustomerData(int $customerId): array
    {
        $customerData = $this->customerRepository->findOneId($customerId);
        $this->customerRepositoryValidator->validateCustomerRepositoryFindOne($customerData);
        return $customerData;
    }

    public function getCurrentOrderData(int $customerId): array
    {
        $result = [];
        $orderDatas = $this->orderRepository->findCurrentCustomerOrder($customerId);
        $this->orderRepositoryValidator->validateOrderRepositoryfindCurrentCustomerOrder($orderDatas);

        foreach ($orderDatas as $data) {
            $id = $data['id'] ?? '';
            $result[$id]['status'] = $data['status'] ?? '';
            $result[$id]['total_price'] = $data['total_price'] ?? '';
            $result[$id]['created_at'] = $data['created_at'] ?? '';
            $result[$id]['item'][] = [
                'product_name' => $data['product_name'] ?? '',
                'product_price' => $data['product_price'] ?? '',
                'product_quantity' => $data['product_quantity'] ?? '',
            ];
        }
        return $result;
    }

    public function getPastOrderData(int $customerId): array
    {
        $result = [];
        $orderDatas = $this->orderRepository->findCompletCustomerOrder($customerId);
        $this->orderRepositoryValidator->validateOrderRepositoryFindCompletCustomerOrder($orderDatas);

        foreach ($orderDatas as $data) {
            $id = $data['id'] ?? '';
            $result[$id]['status'] = $data['status'] ?? '';
            $result[$id]['total_price'] = $data['total_price'] ?? '';
            $result[$id]['created_at'] = $data['created_at'] ?? '';
            $result[$id]['item'][] = [
                'product_name' => $data['product_name'] ?? '',
                'product_price' => $data['product_price'] ?? '',
                'product_quantity' => $data['product_quantity'] ?? '',
            ];
        }
        return $result;
    }

    // ----- admin manage customer done.php -----
    public function validatePostDone(array $post): void
    {
        $this->adminPostValidator->validatePostAdminManageCustomerDone($post);
    }

    public function changeStatus(int $customerId, int $status): void
    {
        $this->customerRepository->updateStatus($customerId, $status);
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

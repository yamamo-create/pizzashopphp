<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Common/DisplayDateWeek.php';
require_once SRC_PATH . '/Repository/AdminLoginEmailRepository.php';
require_once SRC_PATH . '/Repository/AdminLoginIpRepository.php';
require_once SRC_PATH . '/Repository/CustomerLoginEmailRepository.php';
require_once SRC_PATH . '/Repository/CustomerLoginIpRepository.php';
require_once SRC_PATH . '/Admin/Validator/Database/AdminLoginEmailRepositoryValidator.php';
require_once SRC_PATH . '/Admin/Validator/Database/AdminLoginIpRepositoryValidator.php';
require_once SRC_PATH . '/Admin/Validator/Database/CustomerLoginEmailRepositoryValidator.php';
require_once SRC_PATH . '/Admin/Validator/Database/CustomerLoginIpRepositoryValidator.php';

use function App\Common\displayDateWeek;

use App\Repository\AdminLoginEmailRepository;
use App\Repository\AdminLoginIpRepository;
use App\Repository\CustomerLoginEmailRepository;
use App\Repository\CustomerLoginIpRepository;
use App\Admin\Validator\Database\AdminLoginEmailRepositoryValidator;
use App\Admin\Validator\Database\AdminLoginIpRepositoryValidator;
use App\Admin\Validator\Database\CustomerLoginEmailRepositoryValidator;
use App\Admin\Validator\Database\CustomerLoginIpRepositoryValidator;


// ----- admin manage system -----
class AdminManageSystem
{
    private bool $errorFlag;

    private array $errorMessage;

    private AdminLoginEmailRepository $adminLoginEmailRepository;
    private AdminLoginIpRepository $adminLoginIpRepository;
    private CustomerLoginEmailRepository $customerLoginEmailRepository;
    private CustomerLoginIpRepository $customerLoginIpRepository;
    private AdminLoginEmailRepositoryValidator $adminLoginEmailRepositoryValidator;
    private AdminLoginIpRepositoryValidator $adminLoginIpRepositoryValidator;
    private CustomerLoginEmailRepositoryValidator $customerLoginEmailRepositoryValidator;
    private CustomerLoginIpRepositoryValidator $customerLoginIpRepositoryValidator;

    public function __construct()
    {
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->adminLoginEmailRepository = new AdminLoginEmailRepository();
        $this->adminLoginIpRepository = new AdminLoginIpRepository();
        $this->customerLoginEmailRepository = new CustomerLoginEmailRepository();
        $this->customerLoginIpRepository = new CustomerLoginIpRepository();
        $this->adminLoginEmailRepositoryValidator = new AdminLoginEmailRepositoryValidator();
        $this->adminLoginIpRepositoryValidator = new AdminLoginIpRepositoryValidator();
        $this->customerLoginEmailRepositoryValidator = new CustomerLoginEmailRepositoryValidator();
        $this->customerLoginIpRepositoryValidator = new CustomerLoginIpRepositoryValidator();
    }

    // ----- admin manage system admin_ip enter.php -----
    public function getAdminIpAllData(): array
    {
        $adminLoginIpAllData = $this->adminLoginIpRepository->findAll();
        $this->adminLoginIpRepositoryValidator->adminLoginIpRepositoryFindAll($adminLoginIpAllData);
        return $adminLoginIpAllData;
    }
    public function convertDisplayAdminIpAllData(array $adminEmailAllData): array
    {
        $displayData = [];

        if (empty($adminEmailAllData)) {
            $displayData[] = 'nothing';
        }
        if ($adminEmailAllData !== false) {
            foreach ($adminEmailAllData as $value) {
                $id = $value['id'] ?? '';
                $ip = $value['ip'] ?? '';
                $created_at_raw = $value['created_at'] ?? '';
                $success_raw = $value['success'] ?? '';
                $fail_count = $value['fail_count'] ?? '';

                $created_at = DisplayDateWeek($created_at_raw);
                $success = $success_raw == 0 ? '失敗' : '成功';

                $displayData[] =
                    $id . '|' .
                    $ip . '　' .
                    $success . '　' .
                    $fail_count . '　' .
                    $created_at;
            }
        }
        return $displayData;
    }
    // ----- admin manage system admin_ip check_done.php -----
    public function deleteAdminIpAllHistory(): void
    {
        $this->adminLoginIpRepository->deleteAll();
    }

    // ----- admin manage system admin_email enter.php -----
    public function getAdminEmailAllData(): array
    {
        $adminLoginEmailAllData = $this->adminLoginEmailRepository->findAll();
        $this->adminLoginEmailRepositoryValidator->adminLoginEmailRepositoryFindAll($adminLoginEmailAllData);
        return $adminLoginEmailAllData;
    }

    public function convertDisplayAdminEmailAllData(array $adminEmailAllData): array
    {
        $displayData = [];

        if (empty($adminEmailAllData)) {
            $displayData[] = 'nothing';
        }
        if ($adminEmailAllData !== false) {
            foreach ($adminEmailAllData as $value) {
                $id = $value['id'] ?? '';
                $email = $value['email'] ?? '';
                $ip = $value['ip'] ?? '';
                $created_at_raw = $value['created_at'] ?? '';
                $success_raw = $value['success'] ?? '';
                $fail_count = $value['fail_count'] ?? '';

                $created_at = DisplayDateWeek($created_at_raw);
                $success = $success_raw == 0 ? '失敗' : '成功';

                $displayData[] =
                    $id . '|' .
                    $ip . '　' .
                    $email . '　' .
                    $success . '　' .
                    $fail_count . '　' .
                    $created_at;
            }
        }
        return $displayData;
    }

    // ----- admin manage system admin_email check_done.php -----
    public function deleteAdminEmailAllHistory(): void
    {
        $this->adminLoginEmailRepository->deleteAll();
    }

    // ----- admin manage system customer_ip enter.php -----
    public function getCustomerIpAllData(): array
    {
        $customerLoginIpAllData = $this->customerLoginIpRepository->findAll();
        $this->customerLoginIpRepositoryValidator->customerLoginIpRepositoryFindAll($customerLoginIpAllData);
        return $customerLoginIpAllData;
    }
    public function convertDisplayCustomerIpAllData(array $customerEmailAllData): array
    {
        $displayData = [];

        if (empty($customerEmailAllData)) {
            $displayData[] = 'nothing';
        }
        if ($customerEmailAllData !== false) {
            foreach ($customerEmailAllData as $value) {
                $id = $value['id'] ?? '';
                $ip = $value['ip'] ?? '';
                $created_at_raw = $value['created_at'] ?? '';
                $success_raw = $value['success'] ?? '';
                $fail_count = $value['fail_count'] ?? '';

                $created_at = DisplayDateWeek($created_at_raw);
                $success = $success_raw == 0 ? '失敗' : '成功';

                $displayData[] =
                    $id . '|' .
                    $ip . '　' .
                    $success . '　' .
                    $fail_count . '　' .
                    $created_at;
            }
        }
        return $displayData;
    }
    // ----- admin manage system customer_ip check_done.php -----
    public function deleteCustomerIpAllHistory(): void
    {
        $this->customerLoginIpRepository->deleteAll();
    }

    // ----- admin manage system customer_email enter.php -----
    public function getCustomerEmailAllData(): array
    {
        $customerLoginEmailAllData = $this->customerLoginEmailRepository->findAll();
        $this->customerLoginEmailRepositoryValidator->customerLoginEmailRepositoryFindAll($customerLoginEmailAllData);
        return $customerLoginEmailAllData;
    }

    public function convertDisplayCustomerEmailAllData(array $customerEmailAllData): array
    {
        $displayData = [];

        if (empty($customerEmailAllData)) {
            $displayData[] = 'nothing';
        }
        if ($customerEmailAllData !== false) {
            foreach ($customerEmailAllData as $value) {
                $id = $value['id'] ?? '';
                $email = $value['email'] ?? '';
                $ip = $value['ip'] ?? '';
                $created_at_raw = $value['created_at'] ?? '';
                $success_raw = $value['success'] ?? '';
                $fail_count = $value['fail_count'] ?? '';

                $created_at = DisplayDateWeek($created_at_raw);
                $success = $success_raw == 0 ? '失敗' : '成功';

                $displayData[] =
                    $id . '|' .
                    $ip . '　' .
                    $email . '　' .
                    $success . '　' .
                    $fail_count . '　' .
                    $created_at;
            }
        }
        return $displayData;
    }

    // ----- admin manage system customer_email check_done.php -----
    public function deleteCustomerEmailAllHistory(): void
    {
        $this->customerLoginEmailRepository->deleteAll();
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

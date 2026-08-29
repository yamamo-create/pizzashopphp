<?php

namespace App\Customer\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Constants/CustomerStatus.php';
require_once SRC_PATH . '/Customer/Validator/CustomerPostValidator.php';
require_once SRC_PATH . '/Repository/CustomerRepository.php';

use App\Customer\Validator\CustomerPostValidator;
use App\Repository\CustomerRepository;

class CustomerRegister
{
    private bool $errorFlag;

    private array $errorMessage;

    private CustomerPostValidator $customerPostValidator;

    private CustomerRepository $customerRepository;

    public function __construct()
    {
        $this->errorFlag = false;
        $this->errorMessage = [];

        $this->customerPostValidator = new CustomerPostValidator();

        $this->customerRepository = new CustomerRepository();
    }

    // ----- customer register check_done -----
    public function trimCustomerData(array $postData): array
    {
        return [
            'email' => empty($postData['email']) ? '' : trim($postData['email']),
            'password1' => $postData['password1'] ?? '',
            'password2' => $postData['password2'] ?? '',
            'lastname' => empty($postData['lastname']) ? '' : trim($postData['lastname']),
            'firstname' => empty($postData['firstname']) ? '' : trim($postData['firstname']),
            'phone' => empty($postData['phone']) ? '' : trim($postData['phone']),
            'post' => empty($postData['post']) ? '' : trim($postData['post']),
            'address' => empty($postData['address']) ? '' : trim($postData['address']),
        ];
    }

    public function validatePost(array $post): void
    {
        $message = [];
        $message = $this->customerPostValidator->validatePostCustomerRegister($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function isEmailAvailable(string $email): void
    {
        $email = trim($email);
        $existsByEmail = $this->customerRepository->existsByEmail($email);

        if ($this->errorFlag === false && $existsByEmail === true) {
            $this->errorFlag = true;
            $this->errorMessage['email'] = 'そのメールアドレスは、登録に使えません';
            unset($_SESSION['customer']['form']['register']['email']);
        } else {
            $_SESSION['customer']['form']['register']['email'] = $email;
        }
    }

    public function isPassword1and2Same(string $password1, string $password2): void
    {
        if (
            $this->errorFlag === false &&
            $password1 !==  $password2
        ) {
            $this->errorFlag = true;
            $this->errorMessage['password'] = 'パスワードは、確認のため「同じパスワードを２回」入力してください';
        }
    }

    public function createCustomerAccount(array $customerData): void
    {
        $this->customerRepository->insert($customerData);
    }

    public function cleaningSession(): void
    {
        unset($_SESSION['customer']);
    }

    // ----- 共通 -----
    public function getErrorFlag(): bool
    {
        return $this->errorFlag;
    }
    public function getErrorMessage(): array
    {
        return $this->errorMessage;
    }
}

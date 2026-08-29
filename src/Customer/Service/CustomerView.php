<?php

namespace App\Customer\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Customer/Validator/CustomerPostValidator.php';
require_once SRC_PATH . '/Repository/CustomerRepository.php';
require_once SRC_PATH . '/Customer/Validator/Database/CustomerRepositoryValidator.php';

use App\Customer\Validator\CustomerPostValidator;
use App\Repository\CustomerRepository;
use App\Customer\Validator\Database\CustomerRepositoryValidator;

class CustomerView
{
    private bool $errorFlag;

    private array $errorMessage;

    private CustomerPostValidator $customerPostValidator;

    private CustomerRepository $customerRepository;
    private CustomerRepositoryValidator $customerRepositoryValidator;

    public function __construct()
    {
        $this->errorFlag = false;
        $this->errorMessage = [];

        $this->customerPostValidator = new CustomerPostValidator();

        $this->customerRepository = new CustomerRepository();
        $this->customerRepositoryValidator = new CustomerRepositoryValidator();
    }

    // ----- customer view index -----
    // ----- customer view edit enter -----
    // ----- customer view edit complete -----
    // ----- customer view withdraw enter -----
    public function getCustomerData(int $id, string $email): array
    {
        $customerData = $this->customerRepository->findOne($id, $email);
        $this->customerRepositoryValidator->validateCustomerRepositoryFindOne($customerData);
        return $customerData;
    }

    // ----- customer view withdraw check_done -----
    public function validatePostWithdraw(array $post): void
    {
        $message = [];
        $message = $this->customerPostValidator->validatePostCustomerViewWithdraw($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function verifyPassword(string $email, string $password): void
    {
        $rec = $this->customerRepository->findByEmail($email);
        $this->customerRepositoryValidator->validateCustomerRepositoryFindByEmail($rec);

        if (!password_verify($password, $rec['password'])) {
            $this->errorFlag = true;
            $this->errorMessage['error'] = 'パスワードが間違っています';
        }
    }

    public function deleteAccount(int $id, string $email): void
    {
        $this->customerRepository->delete($id, $email);
    }

    // ----- customer view edit check_done -----
    public function trimCustomerEdit(array $postData): array
    {
        return [
            'lastname' => empty($postData['lastname']) ? '' : trim($postData['lastname']),
            'firstname' => empty($postData['firstname']) ? '' : trim($postData['firstname']),
            'phone' => empty($postData['phone']) ? '' : trim($postData['phone']),
            'post' => empty($postData['post']) ? '' : trim($postData['post']),
            'address' => empty($postData['address']) ? '' : trim($postData['address']),
        ];
    }

    public function validatePostEdit(array $post): void
    {
        $message = [];
        $message = $this->customerPostValidator->validatePostCustomerViewEdit($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function editCustomer(array $customerData): void
    {
        $this->customerRepository->updateAccount($customerData);
    }

    // ----- customer view change_pass check_done -----
    public function validatePostChangePass(array $post): void
    {
        $message = [];
        $message = $this->customerPostValidator->validatePostViewChangePass($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function validatePasswordChangePass(string $email, string $oldPassword, string $newPassword1, string $newPassword2): void
    {
        if ($newPassword1 !== $newPassword2) {
            $this->errorFlag = true;
            $this->errorMessage['password1and2_not_match'] = 'パスワードは「新しいパスワード」「新しいパスワードをもう一度入力」を同じものにしてください';
            return;
        }

        $rec = $this->customerRepository->findByEmail($email);

        if ($rec === false) {
            $this->errorFlag = true;
            $this->errorMessage['system'] = '予期せぬエラー';
            return;
        }

        $this->customerRepositoryValidator->validateCustomerRepositoryFindByEmail($rec);

        if (!password_verify($oldPassword, $rec['password'])) { {
                $this->errorFlag = true;
                $this->errorMessage['oldpassword_input_error'] = '現在のパスワードが間違っています';
                return;
            }
        }
    }

    public function changeCustomerPassword(int $id, string $email, string $newPassword): void
    {
        $this->customerRepository->updatePassword($id, $email, $newPassword);
    }

    // ----- customer view change_pass done -----
    // ----- customer view change_email done -----
    public function cleaningCustomerData(): void
    {
        unset($_SESSION['customer']);
    }

    // ----- customer view change_email check_done -----
    public function trimCustomerChangeEmail(array $post): array
    {
        return [
            'email' => empty($post['email']) ? '' : trim($post['email']),
            'password' => empty($post['password']) ? '' : $post['password'],
        ];
    }

    public function validatePostChangeEmail(array $post): void
    {
        $message = [];
        $message = $this->customerPostValidator->validatePostViewChangeEmail($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function validateChangeEmail(string $newEmail, string  $loginEmail, string $password): void
    {
        if ($this->customerRepository->existsByEmail($newEmail)) {
            $this->errorFlag = true;
            $this->errorMessage['email_used'] = 'そのメールアドレスは、使用できません';
            return;
        }

        $rec = $this->customerRepository->findByEmail($loginEmail);

        if ($rec === false) {
            $this->errorFlag = true;
            $this->errorMessage['system'] = '予期せぬエラー';
            return;
        }

        $this->customerRepositoryValidator->validateCustomerRepositoryFindByEmail($rec);

        if (!password_verify($password, $rec['password'])) { {
                $this->errorFlag = true;
                $this->errorMessage['password_input_error'] = 'パスワードが間違っています';
                return;
            }
        }
    }

    public function changeCustomerEmail(int $login_id, string $login_email, string $newEmail)
    {
        $this->customerRepository->updateEmail($login_id, $login_email, $newEmail);
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

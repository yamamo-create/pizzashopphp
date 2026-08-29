<?php

namespace App\Customer\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Customer/Token/CustomerCsrf.php';
require_once SRC_PATH . '/Customer/Validator/CustomerPostValidator.php';

require_once SRC_PATH . '/Repository/CustomerRepository.php';
require_once SRC_PATH . '/Repository/CustomerLoginEmailRepository.php';
require_once SRC_PATH . '/Repository/CustomerLoginIpRepository.php';
require_once SRC_PATH . '/Customer/Validator/Database/CustomerRepositoryValidator.php';
require_once SRC_PATH . '/Customer/Validator/Database/CustomerLoginEmailRepositoryValidator.php';
require_once SRC_PATH . '/Customer/Validator/Database/CustomerLoginIpRepositoryValidator.php';

use App\Customer\Token\CustomerCsrf;
use App\Customer\Validator\CustomerPostValidator;

use App\Repository\CustomerRepository;
use App\Repository\CustomerLoginEmailRepository;
use App\Repository\CustomerLoginIpRepository;
use App\Customer\Validator\Database\CustomerRepositoryValidator;
use App\Customer\Validator\Database\CustomerLoginEmailRepositoryValidator;
use App\Customer\Validator\Database\CustomerLoginIpRepositoryValidator;

// ----- customer login -----
class CustomerLogin
{
    private string $email;
    private string $password;

    private bool $loginFlag;
    private bool $errorFlag;

    private array $errorMessage;

    private CustomerPostValidator $customerPostValidator;

    private CustomerRepository $customerRepository;
    private CustomerLoginEmailRepository $customerEmailRepository;
    private CustomerLoginIpRepository $customerIpRepository;
    private CustomerRepositoryValidator $customerRepositoryValidator;
    private CustomerLoginEmailRepositoryValidator $customerLoginEmailRepositoryValidator;
    private CustomerLoginIpRepositoryValidator $customerLoginIpRepositoryValidator;

    public function __construct()
    {
        $this->loginFlag = false;
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->customerPostValidator = new CustomerPostValidator();

        $this->customerRepository = new CustomerRepository();
        $this->customerIpRepository = new CustomerLoginIpRepository();
        $this->customerEmailRepository = new CustomerLoginEmailRepository();
        $this->customerRepositoryValidator = new CustomerRepositoryValidator;
        $this->customerLoginEmailRepositoryValidator = new CustomerLoginEmailRepositoryValidator;
        $this->customerLoginIpRepositoryValidator = new CustomerLoginIpRepositoryValidator;
    }

    // ----- customer login check_done -----
    public function validatePost(array $post): void
    {
        $message = [];
        $message = $this->customerPostValidator->validatePostCustomerLogin($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function setAccount(string $email, string $password): void
    {
        $this->email = trim($email);
        $this->password = $password;
    }


    public function canLoginIp(): void
    {
        $rec = $this->customerIpRepository->findOne($_SERVER['REMOTE_ADDR']);

        // ----- 記録がない（ログイン OK） -----
        if ($rec === false) {
            return;
        }

        $this->customerLoginIpRepositoryValidator->validateCustomerLoginIpRepositoryFindOne($rec);

        // ----- 失敗回数が０（ログイン OK） -----
        if ($rec['fail_count'] === 0) {
            return;
        }

        $previousTime = strtotime($rec['created_at']);
        $waitingTime = ($_ENV['LOGIN_FAIL_SECONDS'] ** ($rec['fail_count'] + 1));

        $endTime = $previousTime + $waitingTime;

        // ----- 待ち時間が終了している（ログイン OK） -----
        if (time() > $endTime) {
            return;
        }

        // -----　待ち時間が残っている（ログイン NG） ------
        $this->errorFlag = true;
        $this->errorMessage['wait'] = 'ログインを一時的に制限しています。しばらくしてからお試しください。';
        return;
    }

    public function successIp(): void
    {
        $this->customerIpRepository->insert(true, 0);
    }
    public function failIp(): void
    {
        $rec = $this->customerIpRepository->findOne($_SERVER['REMOTE_ADDR']);

        if ($rec === false) {
            $fail = 0;
        } else {
            $this->customerLoginIpRepositoryValidator->validateCustomerLoginIpRepositoryFindOne($rec);
            $fail = $rec['fail_count'];
        }

        $max = $_ENV['LOGIN_FAIL_LIMIT'];

        $currentFail = $fail < $max ? $fail + 1 : $fail;
        $this->customerIpRepository->insert(false, $currentFail);
    }

    public function canLoginEmail(): void
    {
        $rec = $this->customerEmailRepository->findOne($this->email);

        // ----- 記録がない（ログイン OK） -----
        if ($rec === false) {
            return;
        }

        $this->customerLoginEmailRepositoryValidator->validateCustomerLoginEmailRepositoryFindOne($rec);

        // ----- 失敗回数が０（ログイン OK） -----
        if ($rec['fail_count'] === 0) {
            return;
        }

        $previousTime = strtotime($rec['created_at']);
        $waitingTime = ($_ENV['LOGIN_FAIL_SECONDS'] ** ($rec['fail_count'] + 1));

        $endTime = $previousTime + $waitingTime;

        // ----- 待ち時間が終了している（ログイン OK） -----
        if (time() > $endTime) {
            return;
        }

        // -----　待ち時間が残っている（ログイン NG） ------
        $this->errorFlag = true;
        $this->errorMessage['wait'] = 'ログインを一時的に制限しています。しばらくしてからお試しください。';
        return;
    }

    public function successEmail(): void
    {
        $this->customerEmailRepository->insert($this->email, true, 0);
    }
    public function failEmail(): void
    {
        $rec = $this->customerEmailRepository->findOne($this->email);

        if ($rec === false) {
            $fail = 0;
        } else {
            $this->customerLoginEmailRepositoryValidator->validateCustomerLoginEmailRepositoryFindOne($rec);
            $fail = $rec['fail_count'];
        }

        $max = $_ENV['LOGIN_FAIL_LIMIT'];

        $currentFail = $fail < $max ? $fail + 1 : $fail;
        $this->customerEmailRepository->insert($this->email, false, $currentFail);
    }

    public function verifyPassword(): void
    {
        $rec = $this->customerRepository->findByEmail($this->email);

        if ($rec === false) {
            $this->errorFlag = true;
            $this->errorMessage['error'] = 'メールアドレス、パスワードのどちらかが間違っています';
            return;
        }

        $this->customerRepositoryValidator->validateCustomerRepositoryFindByEmail($rec);

        if (password_verify($this->password, $rec['password'])) {
            $this->loginFlag = true;
        } else {
            $this->loginFlag = false;
            $this->errorFlag = true;
            $this->errorMessage['error'] = 'メールアドレス、パスワードのどちらかが間違っています';
            return;
        }
    }

    public function verifyStatus(): void
    {
        if ($this->loginFlag === false || $this->errorFlag === true) {
            return;
        }

        $rec = $this->customerRepository->findByEmail($this->email);

        if ($rec === false) {
            return;
        }

        $this->customerRepositoryValidator->validateCustomerRepositoryFindByEmail($rec);

        if ($rec['status'] == 9) {
            $this->loginFlag = false;
            $this->errorFlag = true;
            $this->errorMessage['error'] = 'アカウント停止中です';
            return;
        }
    }

    public function successLogin(): void
    {
        $rec = $this->customerRepository->findByEmail($this->email);

        if ($rec === false) {
            return;
        }

        $this->customerRepositoryValidator->validateCustomerRepositoryFindByEmail($rec);

        $_SESSION['customer']['login']['is_login'] = true;
        $_SESSION['customer']['login']['id'] = $rec['id'];
        $_SESSION['customer']['login']['email'] = $this->email;
        session_regenerate_id(true);
        CustomerCsrf::regenerate();
    }

    public function getLoginFlag(): bool
    {
        return $this->loginFlag;
    }
    public function geterrorFlag(): bool
    {
        return $this->errorFlag;
    }
    public function geterrorMessage(): array
    {
        return $this->errorMessage;
    }
}

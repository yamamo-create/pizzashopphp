<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Admin/Token/AdminCsrf.php';
require_once SRC_PATH . '/Admin/Validator/AdminPostValidator.php';
require_once SRC_PATH . '/Repository/AdminRepository.php';
require_once SRC_PATH . '/Repository/AdminLoginEmailRepository.php';
require_once SRC_PATH . '/Repository/AdminLoginIpRepository.php';
require_once SRC_PATH . '/Admin/Validator/Database/AdminRepositoryValidator.php';
require_once SRC_PATH . '/Admin/Validator/Database/AdminLoginEmailRepositoryValidator.php';
require_once SRC_PATH . '/Admin/Validator/Database/AdminLoginIpRepositoryValidator.php';

use App\Admin\Token\AdminCsrf;
use App\Admin\Validator\AdminPostValidator;

use App\Repository\AdminRepository;
use App\Repository\AdminLoginEmailRepository;
use App\Repository\AdminLoginIpRepository;
use App\Admin\Validator\Database\AdminRepositoryValidator;
use App\Admin\Validator\Database\AdminLoginEmailRepositoryValidator;
use App\Admin\Validator\Database\AdminLoginIpRepositoryValidator;

// ----- admin login -----
class AdminLogin
{
    private string $email;
    private string $password;

    private bool $loginFlag;
    private bool $errorFlag;

    private array $errorMessage;

    private AdminPostValidator $adminPostValidator;

    private AdminRepository $adminRepository;
    private AdminLoginEmailRepository $adminLoginEmailRepository;
    private AdminLoginIpRepository $adminLoginIpRepository;
    private AdminRepositoryValidator $adminRepositoryValidator;
    private AdminLoginEmailRepositoryValidator $adminLoginEmailRepositoryValidator;
    private AdminLoginIpRepositoryValidator $adminLoginIpRepositoryValidator;

    public function __construct()
    {
        $this->loginFlag = false;
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->adminPostValidator = new AdminPostValidator();

        $this->adminRepository = new AdminRepository();
        $this->adminLoginEmailRepository = new AdminLoginEmailRepository();
        $this->adminLoginIpRepository = new AdminLoginIpRepository();
        $this->adminRepositoryValidator = new AdminRepositoryValidator();
        $this->adminLoginEmailRepositoryValidator = new AdminLoginEmailRepositoryValidator();
        $this->adminLoginIpRepositoryValidator = new AdminLoginIpRepositoryValidator();
    }
    // ----- admin login check_done -----
    public function validatePost(array $post): void
    {
        $message = [];
        $message = $this->adminPostValidator->validatePostAdminLogin($post);
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
        $rec = $this->adminLoginIpRepository->findOne($_SERVER['REMOTE_ADDR']);

        // ----- 記録がない（ログイン OK） -----
        if ($rec === false) {
            return;
        }

        $this->adminLoginIpRepositoryValidator->validateAdminIpRepositoryFindOne($rec);

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
        $this->adminLoginIpRepository->insert(true, 0);
    }
    public function failIp(): void
    {
        $rec = $this->adminLoginIpRepository->findOne($_SERVER['REMOTE_ADDR']);

        if ($rec === false) {
            $fail = 0;
        } else {
            $this->adminLoginIpRepositoryValidator->validateAdminIpRepositoryFindOne($rec);
            $fail = $rec['fail_count'];
        }

        $max = $_ENV['LOGIN_FAIL_LIMIT'];

        $currentFail = $fail < $max ? $fail + 1 : $fail;
        $this->adminLoginIpRepository->insert(false, $currentFail);
    }

    public function canLoginEmail(): void
    {
        $rec = $this->adminLoginEmailRepository->findOne($this->email);

        // ----- 記録がない（ログイン OK） -----
        if ($rec === false) {
            return;
        }

        $this->adminLoginEmailRepositoryValidator->validateAdminEmailRepositoryFindOne($rec);

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
        $this->adminLoginEmailRepository->insert($this->email, true, 0);
    }
    public function failEmail(): void
    {
        $rec = $this->adminLoginEmailRepository->findOne($this->email);

        if ($rec === false) {
            $fail = 0;
        } else {
            $this->adminLoginEmailRepositoryValidator->validateAdminEmailRepositoryFindOne($rec);
            $fail = $rec['fail_count'];
        }

        $max = $_ENV['LOGIN_FAIL_LIMIT'];

        $currentFail = $fail < $max ? $fail + 1 : $fail;
        $this->adminLoginEmailRepository->insert($this->email, false, $currentFail);
    }

    public function verifyPassword(): void
    {
        $rec = $this->adminRepository->findByEmail($this->email);

        if ($rec === false) {
            $this->errorFlag = true;
            $this->errorMessage['error'] = 'メールアドレス、パスワードのどちらかが間違っています';
            return;
        }

        $this->adminRepositoryValidator->validateAdminRepositoryFindByEmail($rec);

        if (password_verify($this->password, $rec['password'])) {
            $this->loginFlag = true;
        } else {
            $this->loginFlag = false;
            $this->errorFlag = true;
            $this->errorMessage['error'] = 'メールアドレス、パスワードのどちらかが間違っています';
            return;
        }
    }

    public function successLogin(): void
    {
        $rec = $this->adminRepository->findByEmail($this->email);

        if ($rec === false) {
            return;
        }

        $this->adminRepositoryValidator->validateAdminRepositoryFindByEmail($rec);

        $_SESSION['admin']['login']['is_login'] = true;
        $_SESSION['admin']['login']['id'] = $rec['id'];
        $_SESSION['admin']['login']['email'] = $this->email;
        $_SESSION['admin']['login']['auth'] = $rec['auth'];
        session_regenerate_id(true);
        adminCsrf::regenerate();
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

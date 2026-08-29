<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Constants/AdminRole.php';
require_once SRC_PATH . '/Admin/Validator/AdminPostValidator.php';
require_once SRC_PATH . '/Repository/AdminRepository.php';
require_once SRC_PATH . '/Admin/Validator/Database/AdminRepositoryValidator.php';

use RuntimeException;

use App\Constants\AdminRole;
use App\Admin\Validator\AdminPostValidator;
use App\Repository\AdminRepository;
use App\Admin\Validator\Database\AdminRepositoryValidator;

// ----- admin manage staff -----
class AdminManageStaff
{
    private bool $errorFlag;

    private array $errorMessage;

    private AdminPostValidator $adminPostValidator;

    private AdminRepository $adminRepository;
    private AdminRepositoryValidator $adminRepositoryValidator;

    public function __construct()
    {
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->adminPostValidator = new AdminPostValidator();

        $this->adminRepository = new AdminRepository();
        $this->adminRepositoryValidator = new AdminRepositoryValidator();
    }
    // ----- admin manage staff list.php -----
    public function getAdminStaffAllData(): array
    {
        $adminStaffAllData = $this->adminRepository->findAll();
        $this->adminRepositoryValidator->validateAdminRepositoryFindAll($adminStaffAllData);
        return $adminStaffAllData;
    }

    // ----- admin manage staff branch.php -----
    public function validatePostBranchChoice(array $post): void
    {
        $this->adminPostValidator->validatePostAdminManageStaffBranchChoice($post);
    }
    public function validatePostBranchChoiceId(array $post): void
    {
        $this->adminPostValidator->validatePostAdminManageStaffBranchChoiceId($post);
    }

    // ----- admin manage staff read enter.php -----
    public function getAdminStaffData(int $choiceId): array
    {
        $adminStaffData = $this->adminRepository->findOne($choiceId);
        $this->adminRepositoryValidator->validateAdminRepositoryFindOne($adminStaffData);
        return $adminStaffData;
    }

    // ----- admin manage staff create check_done.php -----
    public function trimStaffData(array $post): array
    {
        return [
            'auth' =>  empty($post['auth']) ? '' : AdminRole::FORM_VALUES[$post['auth']] ?? '',
            'email' => empty($post['email']) ? '' : trim($post['email']),
            'password1' => $post['password1'] ?? '',
            'password2' => $post['password2'] ?? '',
        ];
    }

    public function validatePostCreate(array $post): void
    {
        $message = [];
        $message = $this->adminPostValidator->validatePostAdminManageStaffCreate($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function isEmailAvailable(string $staffEmail): void
    {
        $existsByEmail = $this->adminRepository->existsByEmail($staffEmail);

        if ($this->errorFlag === false && $existsByEmail === true) {
            $this->errorFlag = true;
            $this->errorMessage['email'] = 'そのメールアドレスは、登録に使えません';
            unset($_SESSION['admin']['form']['create']['email']);
        } else {
            $_SESSION['admin']['form']['create']['email'] = $staffEmail;
        }
    }

    public function isPassword1and2Same(string $staffPassword1, string $staffPassword2): void
    {
        if (
            $this->errorFlag === false &&
            $staffPassword1 !==  $staffPassword2
        ) {
            $this->errorFlag = true;
            $this->errorMessage['password'] = 'パスワードは、確認のため「同じパスワードを２回」入力してください';
        }
    }

    public function createAdminAccount(array $staffData): void
    {
        $this->adminRepository->insert($staffData);
    }

    // ----- admin manage staff delete enter.php -----
    public function checkDeleteYourself(string $login_email, string $email): void
    {
        if ($login_email === $email) {
            $this->errorFlag = true;
            $this->errorMessage['error'] = '自分で自分を消すことはできません';
        }
    }

    // ----- admin manage staff delete check_done.php -----
    public function deleteStaff(int $id, string $email): void
    {
        $this->adminRepository->delete($id, $email);
    }

    // ----- admin manage staff update check_done.php -----
    public function validatePostUpdate(array $post): void
    {
        $message = [];
        $message = $this->adminPostValidator->validatePostAdminManageStaffUpdate($post);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function verifyStaffPassword(?string $login_email, ?string $staffPassword): void
    {
        $rec = $this->adminRepository->findByEmail($login_email);

        if ($rec === false) {
            throw new RuntimeException('email issue', 0);
        }

        $this->adminRepositoryValidator->validateAdminRepositoryFindByEmail($rec);

        if (!password_verify($staffPassword, $rec['password'])) {
            $this->errorFlag = true;
            $this->errorMessage['error'] = 'スタッフのパスワードが間違っています';
        }
    }

    public function isSuperStaffPassword1and2Same(string $newPassword1, string $newPassword2): void
    {
        if (
            $this->errorFlag === false &&
            $newPassword1 !==  $newPassword2
        ) {
            $this->errorFlag = true;
            $this->errorMessage['password'] = '新しいパスワードは、確認のため「同じパスワードを２回」入力してください';
        }
    }

    public function updateSuperStaffPassword(string $newPassword1): void
    {
        $this->adminRepository->updateSuperPassword($newPassword1);
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

<?php

namespace App\Admin\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AdminRepositoryValidator
{
    private RegexCheck $reg;

    // admin
    private readonly array $rulesAdminRepositoryFindOne;
    private readonly array $rulesAdminRepositoryFindAll;
    private readonly array $rulesAdminRepositoryFindByEmail;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- Admin -----
        $this->rulesAdminRepositoryFindOne = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'auth' => ['validator' => fn($v) => in_array($v, [0, 1, 9], true), 'message' => '０、1、9である必要があります'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスの形式ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'deleted_at' => ['validator' => fn($v) => is_string($v) || is_null($v), 'message' => '文字列、NULLではありません'],
        ];

        $this->rulesAdminRepositoryFindAll = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'auth' => ['validator' => fn($v) => in_array($v, [0, 1, 9], true), 'message' => '０、1、9である必要があります'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスの形式ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'deleted_at' => ['validator' => fn($v) => is_string($v) || is_null($v), 'message' => '文字列、NULLではありません'],
        ];

        $this->rulesAdminRepositoryFindByEmail = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'auth' => ['validator' => fn($v) => in_array($v, [0, 1, 9], true), 'message' => '0、1、9である必要があります'],
            'password' => ['validator' => fn($v) => is_string($v), 'message' => 'パスワードの形式が合いません'],
        ];
    }

    private function validator(array $requiredKeys, array $validateData): void
    {
        foreach ($requiredKeys as $key => $rule) {

            if (!array_key_exists($key, $validateData)) {
                throw new InvalidArgumentException("$key がありません");
            }

            if (!$rule['validator']($validateData[$key])) {
                throw new InvalidArgumentException("$key : {$rule['message']}");
            }
        }
    }

    // < Admin >
    // ----- Admin/Service/AdminManageStaff.php -----
    public function validateAdminRepositoryFindOne(array $data): void
    {
        if (empty($data)) {
            throw new InvalidArgumentException("データを読み込めませんでした");
        }
        $this->validator($this->rulesAdminRepositoryFindOne, $data);
    }

    // < Admin >
    // ----- Admin/Service/AdminManageStaff.php -----
    public function validateAdminRepositoryFindAll(array $datas): void
    {
        if (empty($datas)) {
            throw new InvalidArgumentException("data がありません");
        }
        foreach ($datas as $data) {
            $this->validator($this->rulesAdminRepositoryFindAll, $data);
        }
    }

    // < Admin >
    // ----- Admin/Service/AdminLogin.php -----
    // ----- Admin/Service/AdminManageStaff.php -----
    public function validateAdminRepositoryFindByEmail(array $data): void
    {
        $this->validator($this->rulesAdminRepositoryFindByEmail, $data);
    }
}

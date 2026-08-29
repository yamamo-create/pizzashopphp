<?php

namespace App\Customer\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;

class CustomerLoginEmailRepositoryValidator
{
    private RegexCheck $reg;

    private readonly array $rulesCustomerLoginEmailRepositoryFindOne;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- CustomerEmail -----
        $this->rulesCustomerLoginEmailRepositoryFindOne = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスの形式ではありません'],
            'ip' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'success' => ['validator' => fn($v) => in_array($v, [0, 1], true), 'message' => '0または1である必要があります'],
            'fail_count' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
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

    // < CustomerEmail >
    // ----- Customer/Service/CustomerLogin.php -----
    public function validateCustomerLoginEmailRepositoryFindOne(array $data): void
    {
        $this->validator($this->rulesCustomerLoginEmailRepositoryFindOne, $data);
    }
}

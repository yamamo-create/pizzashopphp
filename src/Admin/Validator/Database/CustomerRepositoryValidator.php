<?php

namespace App\Admin\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class CustomerRepositoryValidator
{
    private RegexCheck $reg;

    // customer
    private readonly array $rulesCustomerRepositoryFindOne;
    private readonly array $rulesCustomerRepositoryFindAll;
    private readonly array $rulesCustomerRepositoryFindByEmail;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- Customer -----
        $this->rulesCustomerRepositoryFindOne = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスの形式ではありません'],
            'lastname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字が全角文字ではありません'],
            'firstname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '名前が全角文字ではありません'],
            'phone' => ['validator' => fn($v) => $this->reg->phone($v), 'message' => '電話番号の形式ではありません'],
            'post' => ['validator' => fn($v) => $this->reg->postnumber($v), 'message' => '郵便番号の形式ではありません'],
            'address' => ['validator' => fn($v) => $this->reg->address($v), 'message' => '住所の形式ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [1, 9], true), 'message' => '1または9である必要があります'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'updated_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'deleted_at' => ['validator' => fn($v) => is_string($v) || is_null($v), 'message' => '文字列、NULLではありません'],
        ];

        $this->rulesCustomerRepositoryFindAll = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスの形式ではありません'],
            'lastname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字が全角文字ではありません'],
            'firstname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '名前が全角文字ではありません'],
            'phone' => ['validator' => fn($v) => $this->reg->phone($v), 'message' => '電話番号の形式ではありません'],
            'post' => ['validator' => fn($v) => $this->reg->postnumber($v), 'message' => '郵便番号の形式ではありません'],
            'address' => ['validator' => fn($v) => $this->reg->address($v), 'message' => '住所の形式ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [1, 9], true), 'message' => '1または9である必要があります'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'updated_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'deleted_at' => ['validator' => fn($v) => is_string($v) || is_null($v), 'message' => '文字列、NULLではありません'],
        ];

        $this->rulesCustomerRepositoryFindByEmail = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'password' => ['validator' => fn($v) => is_string($v), 'message' => 'パスワードの形式が合いません'],
            'status' => ['validator' => fn($v) => in_array($v, [1, 9], true), 'message' => '1または9である必要があります'],
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

    // < Customer >
    public function validateCustomerRepositoryFindOne(array $customerData): void
    {
        $this->validator($this->rulesCustomerRepositoryFindOne, $customerData);
    }

    public function validateCustomerRepositoryFindAll(array $customerAllData): void
    {
        if (empty($customerAllData)) {
            throw new InvalidArgumentException("customerAllData がありません");
        }
        foreach ($customerAllData as $customerData) {
            $this->validator($this->rulesCustomerRepositoryFindAll, $customerData);
        }
    }
}

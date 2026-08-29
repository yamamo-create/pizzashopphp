<?php

namespace App\Customer\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;

class OrderRepositoryValidator
{
    private RegexCheck $reg;

    private readonly array $rulesOrder;
    private readonly array $rulesCurrentCustomerOrder;
    private readonly array $rulesCompletedCustomerOrder;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- order -----
        $this->rulesOrder = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'customer_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [0, 1, 3, 4, 9], true), 'message' => '0、1、2、3、4、9である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'updated_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'completed_at' => ['validator' => fn($v) => is_string($v) || is_null($v), 'message' => '文字列、NULLではありません'],
        ];

        $this->rulesCurrentCustomerOrder = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [0, 1, 2, 3, 4, 9], true), 'message' => '0、1、2、3、4、9である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_name' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
        ];

        $this->rulesCompletedCustomerOrder = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [4], true), 'message' => '4である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_name' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
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

    // < Order >
    // ----- Customer/Service/CustomerTop.php -----
    public function validateOrderRepositoryFindOne(array $data): void
    {
        $this->validator($this->rulesOrder, $data);
    }

    // < Order >
    // ----- Customer/Service/CustomerHis.php -----
    public function validateOrderRepositoryFindCurrentCustomerOrder(array $datas): void
    {
        foreach ($datas as $data) {
            $this->validator($this->rulesCurrentCustomerOrder, $data);
        }
    }

    // < Order >
    // ----- Customer/Service/CustomerHis.php -----
    public function validateOrderRepositoryFindCompletedCustomerOrder(array $datas): void
    {
        foreach ($datas as $data) {
            $this->validator($this->rulesCompletedCustomerOrder, $data);
        }
    }
}

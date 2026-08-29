<?php

namespace App\Customer\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;

class OrderitemRepositoryValidator
{
    private RegexCheck $reg;

    private readonly array $rulesOrderitem;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- orderitem -----
        $this->rulesOrderitem = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'order_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'product_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'product_name' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'deleted_at' => ['validator' => fn($v) => is_string($v) || is_null($v), 'message' => '文字列、NULLではありません'],
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

    // < Orderitem >
    // ----- Customer/Service/CustomerTop.php -----
    public function validateOrderitemRepositoryFindAllOrderId(array $data): void
    {
        $this->validator($this->rulesOrderitem, $data);
    }
}

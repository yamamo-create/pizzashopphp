<?php

namespace App\Customer\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;

class ProductRepositoryValidator
{
    private RegexCheck $reg;

    private readonly array $rulesProductRepositoryFindOne;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- product -----
        $this->rulesProductRepositoryFindOne = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'name' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字が全角文字ではありません'],
            'price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'imagename' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'detail' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字が全角文字ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'updated_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
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

    // < Product >
    // ----- Customer/Service/CustomerTop.php -----
    public function validateProductRepositoryFindOne(array $data): void
    {
        $this->validator($this->rulesProductRepositoryFindOne, $data);
    }
}

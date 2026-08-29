<?php

namespace App\Admin\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class ProductRepositoryValidator
{
    private RegexCheck $reg;

    // product
    private readonly array $rulesProductRepositoryFindOne;
    private readonly array $rulesProductRepositoryFindAll;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- product -----
        $this->rulesProductRepositoryFindAll = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'name' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字が全角文字ではありません'],
            'price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'imagename' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'detail' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字が全角文字ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'updated_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'deleted_at' => ['validator' => fn($v) => is_string($v) || is_null($v), 'message' => '文字列、NULLではありません'],
        ];

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
    // ----- Admin/Service/AdminManageProduct.php -----
    // ----- Admin/Service/AdminManagePagecreate.php -----
    public function validateProductRepositoryFindAll(array $productAllData): void
    {
        if (empty($productAllData)) {
            throw new InvalidArgumentException("productAllData がありません");
        }
        foreach ($productAllData as $productData) {
            $this->validator($this->rulesProductRepositoryFindAll, $productData);
        }
    }

    // < Product >
    // ----- Admin/Service/AdminManageProduct.php -----
    public function validateProductRepositoryFindOne(array $productData): void
    {
        if ($productData === false) {
            throw new InvalidArgumentException("productData がありません");
        }
        $this->validator($this->rulesProductRepositoryFindOne, $productData);
    }
}

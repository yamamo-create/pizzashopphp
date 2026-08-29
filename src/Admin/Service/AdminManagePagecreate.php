<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Admin/Validator/AdminPostValidator.php';
require_once SRC_PATH . '/Repository/ProductRepository.php';
require_once SRC_PATH . '/Admin/Validator/Database/ProductRepositoryValidator.php';

use App\Admin\Validator\AdminPostValidator;
use App\Repository\ProductRepository;
use App\Admin\Validator\Database\ProductRepositoryValidator;

use InvalidArgumentException;

// ----- admin manage pagecreate -----
class AdminManagePagecreate
{
    private bool $errorFlag;

    private array $errorMessage;

    private AdminPostValidator $adminPostValidator;

    private ProductRepository $productRepository;
    private ProductRepositoryValidator $productRepositoryValidator;

    public function __construct()
    {
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->adminPostValidator = new AdminPostValidator();

        $this->productRepository = new ProductRepository();
        $this->productRepositoryValidator = new ProductRepositoryValidator();
    }

    // ----- admin manage pagecreate check.php -----
    // ----- admin manage pagecreate done.php -----
    public function validateParseData(array $mealData, array $dessertData): void
    {
        if (empty($mealData) || empty($dessertData)) {
            $this->errorFlag = true;
            $this->errorMessage['menu_nothing'] = 'ごはんメニュー、デザートメニュー両方の商品を選択してください';
            return;
        }

        $mergeData = array_merge($mealData, $dessertData);

        if (count($mergeData) !== count(array_unique($mergeData))) {
            $this->errorFlag = true;
            $this->errorMessage['meal_duplicate'] = 'ごはんメニュー、デザートメニュー両方のメニュー内で同じ商品が選択されています';
        }
    }

    // ----- admin manage pagecreate enter.php -----
    public function getAdminProductAllData(): array
    {
        $adminProductAllData = $this->productRepository->findAll();
        $this->productRepositoryValidator->validateProductRepositoryFindAll($adminProductAllData);
        return $adminProductAllData;
    }

    // ----- admin manage pagecreate check.php -----
    public function validatePost(array $post): void
    {
        $this->adminPostValidator->validatePostAdminManagePagecreate($post);
    }

    public function parsePagecreateData(array $productIds): array
    {
        // 配列の0、'0'を取り除いて、配列番号を振り直す
        $arr = array_values(array_filter($productIds, function ($value) {
            return $value !== 0 && $value !== "0";
        }));

        return array_map('intval', $arr);
    }

    // ----- admin manage pagecreate done.php -----
    public function getProductDatas(array $productIds): array
    {
        $productData = [];
        foreach ($productIds as $id) {
            $productData[] = $this->productRepository->findOne($id);
        }
        return $productData;
    }

    // ----- JSON変換 -----
    //JSON_UNESCAPED_UNICODE　日本語をそのまま表示
    //JSON_PRETTY_PRINT　見やすく整形
    public function encodeJson(array $productData): string
    {
        return json_encode($productData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function  putJsonData(string $productData, string $jsonName): void
    {
        if (file_put_contents(JSON_PATH . '/' . $jsonName, $productData) === false) {
            $this->errorFlag = true;
            $this->errorMessage['system'] = 'ファイルへの書き込みを失敗しました。しばらく経ってからもう一度お試しください';
        }
    }

    // ----- admin manage pagecreate complete.php -----
    public function  getJsonData(string $fileeName): string
    {
        $productData = file_get_contents(JSON_PATH . '/' . $fileeName);

        if ($productData === false) {
            throw new InvalidArgumentException('JSON File Broken');
        }
        return $productData;
    }

    public function decodeJson(string $productData): array
    {
        return json_decode($productData, true);
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

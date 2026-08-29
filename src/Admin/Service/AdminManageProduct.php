<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Config/Env.php';
require_once SRC_PATH . '/Admin/Validator/AdminPostValidator.php';
require_once SRC_PATH . '/Repository/ProductRepository.php';
require_once SRC_PATH . '/Admin/Validator/Database/ProductRepositoryValidator.php';

use finfo;
use InvalidArgumentException;

use App\Admin\Validator\AdminPostValidator;

use App\Repository\ProductRepository;
use App\Admin\Validator\Database\ProductRepositoryValidator;

// ----- admin manage product -----
class AdminManageProduct
{
    private bool $errorFlag;

    private array $errorMessage;

    private array $allowedTypes;

    private AdminPostValidator $adminPostValidator;

    private ProductRepository $productRepository;
    private ProductRepositoryValidator $productRepositoryValidator;

    public function __construct()
    {
        $this->errorFlag = false;

        $this->errorMessage = [];

        $this->allowedTypes = [
            // 'image/jpeg' => 'jpg',
            // 'image/gif' => 'gif',
            'image/png' => 'png'
        ];

        $this->adminPostValidator = new AdminPostValidator();

        $this->productRepository = new ProductRepository();
        $this->productRepositoryValidator = new ProductRepositoryValidator();
    }

    // ----- admin manage product list.php -----
    // ----- admin manage product branch.php -----
    // ----- admin manage product update check_done.php -----
    // ----- admin manage product delete check_done.php -----
    public function  getJsonData(string $fileeName): string
    {
        $productData = '';
        if (file_exists(JSON_PATH . '/' . $fileeName)) {

            $productData = file_get_contents(JSON_PATH . '/' . $fileeName);
            if ($productData === false) {
                throw new InvalidArgumentException('JSON File Broken');
            }
        }
        return $productData;
    }

    public function decodeJson(string $productData): array
    {
        $result = [];
        if ($productData !== '') {
            $result = json_decode($productData, true);
        }
        return $result;
    }

    public function getUseProductIds(array $meal, array $dessert): array
    {
        $result = [];

        if (!empty($meal)) {
            foreach ($meal as $product) {
                $result[] = $product['id'];
            }
        }
        if (!empty($dessert)) {
            foreach ($dessert as $product) {
                $result[] = $product['id'];
            }
        }
        return $result;
    }

    // ----- admin manage product list.php -----
    public function getAdminProductAllData(): array
    {
        $adminProductAllData = $this->productRepository->findAll();
        $this->productRepositoryValidator->validateProductRepositoryFindAll($adminProductAllData);
        return $adminProductAllData;
    }

    public function markUsedProducts(array $productDatas, array $useIds): array
    {
        if (empty($productDatas)) {
            return [];
        }

        $idsMap = array_flip($useIds);

        foreach ($productDatas as $key => $product) {

            if (isset($idsMap[$product['id']])) {
                $productDatas[$key]['is_used'] = '掲載中';
            } else {
                $productDatas[$key]['is_used'] = '';
            }
        }
        return $productDatas;
    }

    // ----- admin manage product branch.php -----
    public function validatePostBranchChoice(array $post): void
    {
        $this->adminPostValidator->validatePostAdminManageProductBranchChoice($post);
    }
    public function validatePostBranchChoiceId(array $post): void
    {
        $this->adminPostValidator->validatePostAdminManageProductBranchChoiceId($post);
    }
    public function validateProductNotUsedBranch(array $useProductIds, int $choiceId): void
    {
        if (empty($useProductIds)) {
            return;
        }

        foreach ($useProductIds as $useId) {
            if (intval($useId) === intval($choiceId)) {
                $this->errorFlag = true;
                $this->errorMessage['product_used'] = '掲載中の商品は、修正、削除ができません';
            }
        }
    }

    // ----- admin manage Product read enter.php -----
    public function getAdminProductData(string $choiceId): array
    {
        $adminProductData = $this->productRepository->findOne($choiceId);
        $this->productRepositoryValidator->validateProductRepositoryFindOne($adminProductData);
        return $adminProductData;
    }

    // ----- admin manage product create check_done.php -----
    public function trimPostCreate(array $post): array
    {
        return [
            'name' =>  empty($post['name']) ? '' : trim($post['name']),
            'price' => $post['price'] ?? '',
            'detail' => empty($post['detail']) ? '' : trim($post['detail']),
        ];
    }

    public function validatePostCreate(array $post, array $file): void
    {
        $message = [];
        $message = $this->adminPostValidator->validatePostAdminManageProductCreate($post, $file);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function validateImageFileCreate(array $file): void
    {
        $imageFile['image'] = $file['image'] ?? null;

        if (is_null($imageFile['image'])) {
            $this->errorFlag = true;
            $this->errorMessage['image_ng'] = '画像を正しく入力して下さい';
            return;
        }

        if ($imageFile['image']['error'] !== UPLOAD_ERR_OK) {
            $this->errorFlag = true;
            $this->errorMessage['image_error'] = '画像を正しく入力して下さい';
            return;
        }

        // ファイルサイズ制限（例：2MB）
        if ($imageFile['image']['size'] > 2 * 1024 * 1024) {
            $this->errorFlag = true;
            $this->errorMessage['imagename_large'] = '画像が大きすぎます';
            return;
        }

        // MIMEタイプチェック（重要）
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($imageFile['image']['tmp_name']);

        if (array_key_exists($mimeType, $this->allowedTypes) === false) {
            $this->errorFlag = true;
            $this->errorMessage['imagename_type'] = '画像の拡張子は「png」のみ使えます';
        }
    }

    public function createProduct(array $productData, array $imageFile): void
    {
        // ファイル名は、ランダム生成
        $fileName = bin2hex(random_bytes(16));

        // MIMEタイプチェック（重要）
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($imageFile['image']['tmp_name']);

        // 拡張子
        $extension = $this->allowedTypes[$mimeType];

        $imagename = $fileName . '.' . $extension;

        // 保存先パス・IMAGE_PATH =（src/Config/Path.php）
        $destination = IMAGE_PATH . '/' . $imagename;

        $productData['imagename'] = $imagename;
        $productData['destination'] = $destination;
        $productData['tmp_name'] = $imageFile['image']['tmp_name'] ?? null;

        $this->productRepository->insert($productData);
    }

    // ----- admin manage product delete check_done.php -----
    public function validateProductNotUsedDelete(array $useProductIds, int $choiceId): void
    {
        if (empty($useProductIds)) {
            return;
        }

        foreach ($useProductIds as $useId) {
            if (intval($useId) === intval($choiceId)) {
                $this->errorFlag = true;
                $this->errorMessage['product_used'] = '掲載中の商品は削除ができません';
            }
        }
    }
    public function deleteProduct(int $choiceId, string $imagename): void
    {
        $productDestination = IMAGE_PATH . '/' . $imagename;
        $this->productRepository->delete($choiceId, $productDestination);
    }

    // ----- admin manage product update check_done.php -----
    public function validateProductNotUsedUpdate(array $useProductIds, int $choiceId): void
    {
        if (empty($useProductIds)) {
            return;
        }

        foreach ($useProductIds as $useId) {
            if (intval($useId) === intval($choiceId)) {
                $this->errorFlag = true;
                $this->errorMessage['product_used'] = '掲載中の商品は修正ができません';
            }
        }
    }
    public function trimPostUpdate(array $post): array
    {
        return [
            'name' =>  empty($post['name']) ? '' : trim($post['name']),
            'price' => $post['price'] ?? '',
            'detail' => empty($post['detail']) ? '' : trim($post['detail']),
        ];
    }

    public function validatePostUpdate(array $post, array $file): void
    {
        $message = [];
        $message = $this->adminPostValidator->validatePostAdminManageProductUpdate($post, $file);
        if (!empty($message)) {
            $this->errorFlag = true;
            $this->errorMessage = $message;
        }
    }

    public function validateImageFileUpdate(array $file): void
    {
        $imageFile['image'] = $file['image'] ?? null;

        if (is_null($imageFile['image'])) {
            $this->errorFlag = true;
            $this->errorMessage['image_ng'] = '画像を正しく入力して下さい';
            return;
        }

        if ($imageFile['image']['error'] !== UPLOAD_ERR_OK) {
            $this->errorFlag = true;
            $this->errorMessage['image_error'] = '画像を正しく入力して下さい';
            return;
        }

        // ファイルサイズ制限（例：2MB）
        if ($imageFile['image']['size'] > 2 * 1024 * 1024) {
            $this->errorFlag = true;
            $this->errorMessage['imagename_large'] = '画像が大きすぎます';
            return;
        }

        // MIMEタイプチェック（重要）
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($imageFile['image']['tmp_name']);

        if (array_key_exists($mimeType, $this->allowedTypes) === false) {
            $this->errorFlag = true;
            $this->errorMessage['imagename_type'] = '画像の拡張子は「png」のみ使えます';
        }
    }

    public function updateProduct(int $id, array $productData, array $imageFile, string  $oldImagename): void
    {
        // ファイル名は、ランダム生成
        $fileName = bin2hex(random_bytes(16));

        // MIMEタイプチェック（重要）
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($imageFile['image']['tmp_name']);

        // 拡張子
        $extension = $this->allowedTypes[$mimeType];

        $imagename = $fileName . '.' . $extension;

        // 保存先パス・IMAGE_PATH =（src/Config/Path.php）
        $newImagedestination = IMAGE_PATH . '/' . $imagename;
        $oldImageDestination = IMAGE_PATH . '/' . $oldImagename;

        $productData['id'] = $id;
        $productData['imagename'] = $imagename;
        $productData['tmp_name'] = $imageFile['image']['tmp_name'] ?? null;
        $productData['new_destination'] = $newImagedestination;
        $productData['old_destination'] = $oldImageDestination;

        $this->productRepository->update($productData);
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

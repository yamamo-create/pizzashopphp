<?php

namespace App\Admin\Validator;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;

class AdminPostValidator
{
    private RegexCheck $reg;

    public function __construct()
    {
        $this->reg = new RegexCheck();
    }

    private function validateEnter(array $required, array $post): array
    {
        $errorMessage = [];

        foreach ($required as $key => $terms) {
            if (!isset($post[$key]) || $post[$key] === '') {
                $errorMessage[$key] = $terms['message'];
            }
        }
        return $errorMessage;
    }

    private function validateCharacterCount(array $required, array $post): array
    {
        $errorMessage = [];

        foreach ($required as $key => $terms) {
            if (!is_string($post[$key]) || mb_strlen($post[$key]) > $terms['max']) {
                $errorMessage[$key] = $terms['message'];
            }
        }
        return $errorMessage;
    }
    private function validateDetail(array $required, array $post): array
    {
        $errorMessage = [];

        foreach ($required as $key => $rule) {
            if (!$rule['validator']($post[$key])) {
                $errorMessage[$key] = $rule['message'];
            }
        }
        return $errorMessage;
    }

    private function validatorSystemEnter(array $required, array $post): void
    {
        foreach ($required as $key => $terms) {
            if (!isset($post[$key])) {
                throw new InvalidArgumentException($terms['message']);
            }
        }
    }
    private function validatorSystemCharacterCount(array $required, array $post): void
    {
        foreach ($required as $key => $terms) {
            if (!is_string($post[$key]) || mb_strlen($post[$key]) > $terms['max']) {
                throw new InvalidArgumentException($terms['message']);
            }
        }
    }
    private function validatorSystemDetail(array $required, array $post): void
    {
        foreach ($required as $key => $rule) {
            if (!$rule['validator']($post[$key])) {
                throw new InvalidArgumentException("$key : {$rule['message']}");
            }
        }
    }

    // < adminLogin.php >
    // ----- admin login check_done.php -----
    // ----- $_POST['email'] -----
    // ----- $_POST['password'] -----
    public function validatePostAdminLogin(array $post): array
    {
        $errorMessage = [];

        $required = [
            'email' => ['message' => 'Email(ID) を入力してください'],
            'password' => ['message' => 'パスワード を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'email' => ['max' => 255, 'message' => 'メールアドレスもしくはパスワードが間違っています'],
            'password' => ['max' => 255, 'message' => 'メールアドレスもしくはパスワードが間違っています'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'email' => ['validator' => fn($v) => is_string($v) && filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスもしくはパスワードが間違っています'],
            'password' => ['validator' => fn($v) => $this->reg->password($v), 'message' => 'メールアドレスもしくはパスワードが間違っています'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < adminManageStaff.php >
    // ----- admin manage staff branch.php -----
    // ----- $_POST['choice'] -----
    public function validatePostAdminManageStaffBranchChoice(array $post): void
    {
        $required = [
            'choice' => ['message' => "choice Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice' => [
                'validator' => fn($v) => in_array($v, ['create', 'read', 'update', 'delete'], true),
                'message' => 'ONLY = create、read、update、delete'
            ],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminManageStaff.php >
    // ----- admin manage staff branch.php -----
    // ----- $_POST['choice_id'] -----
    public function validatePostAdminManageStaffBranchChoiceId(array $post): void
    {
        $required = [
            'choice_id' => ['message' => "choice_id Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' =>  'Not INT ,$v is zero or minus'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminManageStaff.php >
    // ----- admin manage staff create check_done.php -----
    // ----- $_POST['auth'] -----
    // ----- $_POST['email'] -----
    // ----- $_POST['password1'] -----
    // ----- $_POST['password2'] -----
    public function validatePostAdminManageStaffCreate(array $post): array
    {
        $required = [
            'auth' => ['message' => "auth Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'auth' => ['validator' => fn($v) => in_array($v, [0, 1, 9], true), 'message' => 'auth Fault'],
        ];
        $this->validatorSystemDetail($required, $post);

        $errorMessage = [];

        $required = [
            'email' => ['message' => 'ログインID（Email） を入力してください'],
            'password1' => ['message' => 'パスワード を入力してください'],
            'password2' => ['message' => '同じパスワードをもう一度 を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        $required = [
            'email' => ['max' => 255, 'message' => 'ログインID（Email）は、255文字以内でお願いします'],
            'password1' => ['max' => 255, 'message' => 'パスワードは、8～64文字以内でお願いします'],
            'password2' => ['max' => 255, 'message' => '同じパスワードをもう一度は、8～64文字以内でお願いします'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'email' => ['validator' => fn($v) => is_string($v) && filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスの形式が間違っています'],
            'password1' => ['validator' => fn($v) => $this->reg->password($v), 'message' => 'パスワードの形式が間違っています'],
            'password2' => ['validator' => fn($v) => $this->reg->password($v), 'message' => '同じパスワードをもう一度の形式が間違っています'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < adminManageStaff.php >
    // ----- admin manage staff update check_done.php -----
    // ----- $_POST['staff_password'] -----
    // ----- $_POST['new_password1'] -----
    // ----- $_POST['new_password2'] -----
    public function validatePostAdminManageStaffUpdate(array $post): array
    {
        $errorMessage = [];

        $required = [
            'staff_password' => ['message' => 'ログイン中のスタッフのパスワード を入力してください'],
            'new_password1' => ['message' => '新しいスーパーアカウントのパスワード を入力してください'],
            'new_password2' => ['message' => '新しいスーパーアカウントのパスワードをもう一度 を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'staff_password' => ['max' => 255, 'message' => 'ログイン中のスタッフのパスワード が長すぎます'],
            'new_password1' => ['max' => 255, 'message' => '新しいスーパーアカウントのパスワードは、8～64文字以内でお願いします'],
            'new_password2' => ['max' => 255, 'message' => '新しいスーパーアカウントのパスワードをもう一度は、8～64文字以内でお願いします'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'staff_password' => ['validator' => fn($v) => $this->reg->password($v), 'message' => 'ログイン中のスタッフのパスワード の形式が間違っています'],
            'new_password1' => ['validator' => fn($v) => $this->reg->password($v), 'message' => '新しいスーパーアカウントのパスワード の形式が間違っています'],
            'new_password2' => ['validator' => fn($v) => $this->reg->password($v), 'message' => '新しいスーパーアカウントのパスワードをもう一度 の形式が間違っています'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < adminManageProduct.php >
    // ----- admin manage product branch.php -----
    // ----- $_POST['choice'] -----
    public function validatePostAdminManageProductBranchChoice(array $post): void
    {
        $required = [
            'choice' => ['message' => "choice Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice' => ['validator' => fn($v) => in_array($v, ['create', 'read', 'update', 'delete'], true), 'message' => 'ONLY = create、read、update、delete'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminManageProduct.php >
    // ----- admin manage product branch.php -----
    // ----- $_POST['choice_id'] -----
    public function validatePostAdminManageProductBranchChoiceId(array $post): void
    {
        $required = [
            'choice_id' => ['message' => "choice_id Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' =>  'Not INT ,$v is zero or minus'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminManageProduct.php >
    // ----- admin manage product create enter.php -----
    // ----- $_POST['name'] -----
    // ----- $_POST['price'] -----
    // ----- $_POST['detail'] -----
    // ----- $_FILES['image'] -----
    public function validatePostAdminManageProductCreate(array $post, array $file): array
    {
        $errorMessage = [];

        $required = [
            'name' => ['message' => '商品名 を入力してください'],
            'price' => ['message' => '価格 を入力してください'],
            'detail' => ['message' => '商品説明 を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (
            empty($file['image']['tmp_name']) ||
            !is_uploaded_file($file['image']['tmp_name'])
        ) {
            $errorMessage['image'] = 'ファイルがアップロードされていないか、無効なファイルです';
        }

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'name' => ['max' => 100, 'message' => '商品名は、100文字以内でお願いします'],
            'price' => ['max' => 6, 'message' => '価格は、6桁まででお願いします'],
            'detail' => ['max' => 200, 'message' => '商品説明は、200文字以内でお願いします'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'name' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '商品名は、全角文字でお願いします'],
            'price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '価格は、0を含む半角数字でお願いします'],
            'detail' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '商品説明は、全角文字でお願いします'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < adminManageProduct.php >
    // ----- admin manage product update enter.php -----
    // ----- $_POST['name'] -----
    // ----- $_POST['price'] -----
    // ----- $_POST['detail'] -----
    // ----- $_FILES['image'] -----
    public function validatePostAdminManageProductUpdate(array $post, array $file): array
    {
        $errorMessage = [];

        $required = [
            'name' => ['message' => '商品名 を入力してください'],
            'price' => ['message' => '価格 を入力してください'],
            'detail' => ['message' => '商品説明 を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (
            empty($file['image']['tmp_name']) ||
            !is_uploaded_file($file['image']['tmp_name'])
        ) {
            $errorMessage['image'] = 'ファイルがアップロードされていないか、無効なファイルです';
        }

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'name' => ['max' => 100, 'message' => '商品名は、100文字以内でお願いします'],
            'price' => ['max' => 6, 'message' => '価格は、6桁まででお願いします'],
            'detail' => ['max' => 200, 'message' => '商品説明は、200文字以内でお願いします'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'name' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '商品名は、全角文字でお願いします'],
            'price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '価格は、0を含む半角数字でお願いします'],
            'detail' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '商品説明は、全角文字でお願いします'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < adminManagePagecreate.php >
    // ----- admin manage pagecreate check.php -----
    // ----- $_POST['meal'][] -----
    // ----- $_POST['dessert'][] -----
    public function validatePostAdminManagePagecreate(array $post): void
    {
        $required = [
            'meal' => ['message' => "meal Not exist"],
            'dessert' => ['message' => "dessert Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        if (!is_array($post['meal'])) {
            throw new InvalidArgumentException('Not Array');
        }

        if (!is_array($post['dessert'])) {
            throw new InvalidArgumentException('Not Array');
        }

        foreach ($post['meal'] as  $productId) {
            if (!ctype_digit(strval($productId))) {
                throw new InvalidArgumentException('Not Number');
            }
        }

        foreach ($post['dessert'] as  $productId) {
            if (!ctype_digit(strval($productId))) {
                throw new InvalidArgumentException('Not Number');
            }
        }
    }

    // < adminManageCustomer.php >
    // ----- admin manage customer check.php -----
    // ----- $_POST['choice_id'] -----
    public function validatePostAdminManageCustomerCheck(array $post): void
    {
        $required = [
            'choice_id' => ['message' => "choice_id Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' =>  'Not INT, $v is zero or minus'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminManageCustomer.php >
    // ----- admin manage customer done.php -----
    // ----- $_POST['choice'] -----
    public function validatePostAdminManageCustomerDone(array $post): void
    {
        $required = [
            'choice' => ['message' => "choice Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice' => ['validator' => fn($v) => in_array($v, ['possible', 'stop'], true), 'message' => 'ONLY = possible, stop'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminOrder.php >
    // ----- admin order check_done.php -----
    // ----- $_POST['choice_id'] -----
    // ----- $_POST['choice'] -----
    public function validatePostAdminOrderCheck(array $post): void
    {
        $required = [
            'choice_id' => ['message' => "choice_id Not exist"],
            'choice' => ['message' => "choice Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' =>  'Not INT, $v is zero or minus'],
            'choice' => ['validator' => fn($v) => in_array($v, ['received', 'working', 'shipped', 'completed'], true), 'message' => 'ONLY = received, working, shipped, completed'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminSales.php >
    // ----- admin sales check_done.php -----
    // ----- $_POST['choice_id'] -----
    // ----- $_POST['choice'] -----
    public function validatePostAdminSalesCheck(array $post): void
    {
        $required = [
            'choice_id' => ['message' => "choice_id Not exist"],
            'choice' => ['message' => "choice Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' =>  'Not INT, $v is zero or minus'],
            'choice' => ['validator' => fn($v) => in_array($v, ['received', 'canceled'], true), 'message' => 'ONLY = received, canceled'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < adminCancelOrder.php >
    // ----- admin cancel_order check_done.php -----
    // ----- $_POST['choice_id'] -----
    // ----- $_POST['choice'] -----
    public function validatePostAdminCancelOrder(array $post): void
    {
        $required = [
            'choice_id' => ['message' => "choice_id Not exist"],
            'choice' => ['message' => "choice Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' =>  'Not INT, $v is zero or minus'],
            'choice' => ['validator' => fn($v) => in_array($v, ['received'], true), 'message' => 'ONLY = received'],
        ];
        $this->validatorSystemDetail($required, $post);
    }
}

<?php

namespace App\Customer\Validator;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;
use InvalidArgumentException;

class CustomerPostValidator
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

    // < customerLogin.php >
    // ----- customer login check_done -----
    // ----- $_POST['email'] -----
    // ----- $_POST['password'] -----
    public function validatePostCustomerLogin(array $post): array
    {
        $errorMessage = [];

        $required = [
            'email' => ['message' => 'Email（ I D ) を入力してください'],
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

    // < customerTop.php >
    // ----- customer/top/cart_in.php -----
    // ----- customer/top/cart_out.php -----
    // ----- customer/top/cart/cart_delete.php -----
    // ----- $_POST['product_id'] -----
    public function validatePostCustomerTopCart(array $post): void
    {
        $required = [
            'product_id' => ['message' => "product_id Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'product_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' =>  'Not INT ,$v is zero or minus'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < customerTop.php >
    // ----- customer/top/cart/cart_quantity.php -----
    // ----- $_POST['product_id'] -----
    // ----- $_POST['product_quantity'] -----
    public function validatePostCustomerTopCartCartQuantity(array $post): void
    {
        $required = [
            'product_id' => ['message' => "product_id Not exist"],
            'product_quantity' => ['message' => "product_quantity Not exist"],
        ];
        $this->validatorSystemEnter($required, $post);

        $required = [
            'product_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => 'Not INT ,$v is zero or minus'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => 'Not INT'],
        ];
        $this->validatorSystemDetail($required, $post);
    }

    // < CustomerRegister.php >
    // ----- customer/register/check_done.php -----
    // ----- $_POST['email'] -----
    // ----- $_POST['password1'] -----
    // ----- $_POST['password2'] -----
    // ----- $_POST['lastname'] -----
    // ----- $_POST['firstname'] -----
    // ----- $_POST['phone'] -----
    // ----- $_POST['post'] -----
    // ----- $_POST['address'] -----
    public function validatePostCustomerRegister(array $post): array
    {
        $errorMessage = [];

        $required = [
            'email' => ['message' => 'ログインID（Email） を入力してください'],
            'password1' => ['message' => 'パスワード を入力してください'],
            'password2' => ['message' => '同じパスワードをもう一度 を入力してください'],
            'lastname' => ['message' => '苗字 を入力してください'],
            'firstname' => ['message' => '名前 を入力してください'],
            'phone' => ['message' => '電話番号 を入力してください'],
            'post' => ['message' => '郵便番号 を入力してください'],
            'address' => ['message' => '住所 を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'email' => ['max' => 255, 'message' => 'メールアドレスは、255文字以内でお願いします'],
            'password1' => ['max' => 255, 'message' => 'パスワードは、8～64文字以内でお願いします'],
            'password2' => ['max' => 255, 'message' => '同じパスワードは、8～64文字以内でお願いします'],
            'lastname' => ['max' => 50, 'message' => '苗字は、50文字以内でお願いします'],
            'firstname' => ['max' => 50, 'message' => '名前は、50文字以内でお願いします'],
            'phone' => ['max' => 13, 'message' => '電話番号は、13文字以内でお願いします'],
            'post' => ['max' => 8, 'message' => '郵便番号は、8文字以内でお願いします'],
            'address' => ['max' => 200, 'message' => '住所は、200文字以内でお願いします'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'email' => ['validator' => fn($v) => is_string($v) && filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスを正しく入力して下さい'],
            'password1' => ['validator' => fn($v) => $this->reg->password($v), 'message' => 'パスワードは「半角大英字、半角小英字、半角数字を両方利用した（8〜64文字）」でお願いします'],
            'password2' => ['validator' => fn($v) => $this->reg->password($v), 'message' => '同じパスワードは「パスワードと同じもの」でお願いします'],
            'lastname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字は全角文字でお願いします'],
            'firstname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '名前は全角文字でお願いします'],
            'phone' => ['validator' => fn($v) => $this->reg->phone($v), 'message' => '電話番号は「半角数字、半角ハイフン」（例：○○○-○○○）でお願いします'],
            'post' => ['validator' => fn($v) => $this->reg->postnumber($v), 'message' => '郵便番号は「半角数字、半角ハイフン」（例：○○○-○○○）でお願いします'],
            'address' => ['validator' => fn($v) => $this->reg->address($v), 'message' => '住所は「全角文字（ひらがな・カタカナ・漢字）全角数字、全角英数、全角ハイフン、全角スペース、「・」（中点）、「（）」全角括弧」でお願いします'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < CustomerView.php >
    // ----- customer/view/withdraw/check_done.php -----
    // ----- $_POST['password'] -----
    public function validatePostCustomerViewWithdraw(array $post): array
    {
        $errorMessage = [];

        $required = [
            'password' => ['message' => 'パスワード を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'password' => ['max' => 255, 'message' => 'パスワードが長すぎます'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'password' => ['validator' => fn($v) => $this->reg->password($v), 'message' => 'パスワードの形式が間違っています'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < CustomerView.php >
    // ----- customer/view/edit/check_done.php -----
    // ----- $_POST['lastname'] -----
    // ----- $_POST['firstname'] -----
    // ----- $_POST['phone'] -----
    // ----- $_POST['post'] -----
    // ----- $_POST['address'] -----
    public function validatePostCustomerViewEdit(array $post): array
    {
        $errorMessage = [];

        $required = [
            'lastname' => ['message' => '苗字 を入力してください'],
            'firstname' => ['message' => '名前 を入力してください'],
            'phone' => ['message' => '電話番号 を入力してください'],
            'post' => ['message' => '郵便番号 を入力してください'],
            'address' => ['message' => '住所 を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'lastname' => ['max' => 50, 'message' => '苗字は、50文字以内でお願いします'],
            'firstname' => ['max' => 50, 'message' => '名前は、50文字以内でお願いします'],
            'phone' => ['max' => 13, 'message' => '電話番号は、13文字以内でお願いします'],
            'post' => ['max' => 8, 'message' => '郵便番号は、8文字以内でお願いします'],
            'address' => ['max' => 200, 'message' => '住所は、200文字以内でお願いします'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'lastname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '苗字は全角文字でお願いします'],
            'firstname' => ['validator' => fn($v) => $this->reg->fullchar($v), 'message' => '名前は全角文字でお願いします'],
            'phone' => ['validator' => fn($v) => $this->reg->phone($v), 'message' => '電話番号は「半角数字、半角ハイフン」（例：○○○-○○○）でお願いします'],
            'post' => ['validator' => fn($v) => $this->reg->postnumber($v), 'message' => '郵便番号は「半角数字、半角ハイフン」（例：○○○-○○○）でお願いします'],
            'address' => ['validator' => fn($v) => $this->reg->address($v), 'message' => '住所は「全角文字（ひらがな・カタカナ・漢字）全角数字、全角英数、全角ハイフン、全角スペース、「・」（中点）、「（）」全角括弧」でお願いします'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < CustomerView.php >
    // ----- customer/view/change_email/check_done.php -----
    // ----- $_POST['email'] -----
    // ----- $_POST['password'] -----
    public function validatePostViewChangeEmail(array $post): array
    {
        $errorMessage = [];

        $required = [
            'email' => ['message' => '変更したいメールアドレス を入力してください'],
            'password' => ['message' => 'パスワード を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'email' => ['max' => 255, 'message' => '変更したいメールアドレスは、255文字以内でお願いします'],
            'password' => ['max' => 255, 'message' => 'パスワードが長すぎます'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'email' => ['validator' => fn($v) => is_string($v) && filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'メールアドレスの形式ではありません'],
            'password' => ['validator' => fn($v) => $this->reg->password($v), 'message' => 'パスワードの形式ではありません'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }

    // < CustomerView.php >
    // ----- customer/view/change_pass/check_done.php -----
    // ----- $_POST['old_password'] -----
    // ----- $_POST['new_password1'] -----
    // ----- $_POST['new_password2'] -----
    public function validatePostViewChangePass(array $post): array
    {
        $errorMessage = [];

        $required = [
            'old_password' => ['message' => '現在のパスワード を入力してください'],
            'new_password1' => ['message' => '新しいパスワード を入力してください'],
            'new_password2' => ['message' => '新しいパスワードをもう一度 を入力してください'],
        ];
        $errorMessage = $this->validateEnter($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'old_password' => ['max' => 255, 'message' => '現在のパスワードが長すぎます'],
            'new_password1' => ['max' => 255, 'message' => '新しいパスワードは、8～64文字以内でお願いします'],
            'new_password2' => ['max' => 255, 'message' => '新しいパスワードをもう一度は、8～64文字以内でお願いします'],
        ];
        $errorMessage = $this->validateCharacterCount($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }

        $required = [
            'old_password' => ['validator' => fn($v) => $this->reg->password($v), 'message' => 'パスワードの形式ではありません'],
            'new_password1' => ['validator' => fn($v) => $this->reg->password($v), 'message' => '新しいパスワードは「半角大英字、半角小英字、半角数字を両方利用した（8〜64文字）」でお願いします'],
            'new_password2' => ['validator' => fn($v) => $this->reg->password($v), 'message' => '新しいパスワードをもう一度は「新しいパスワードと同じもの」でお願いします'],
        ];
        $errorMessage = $this->validateDetail($required, $post);

        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return [];
    }
}

<?php

namespace App\Customer\Validator;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class CustomerSessionValidator
{
    private RegexCheck $reg;

    private readonly array $rulesIsLogin;
    private readonly array $rulesLoginIdEmail;
    private readonly array $rulesTotalPrice;
    private readonly array $rulesCartIdQuantity;
    private readonly array $rulesOrderId;
    private readonly array $rulesFlashMessage;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        $this->rulesIsLogin = [
            'is_login' => ['validator' => fn($v) => !is_null($v) && $v === true, 'message' => 'Session time out OR Direct URL'],
        ];

        $this->rulesLoginIdEmail = [
            'login_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => 'Session is Broken'],
            'login_email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'Session is Broken'],
        ];

        $this->rulesTotalPrice = [
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
        ];

        $this->rulesCartIdQuantity = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
        ];

        $this->rulesOrderId = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '正の整数ではありません'],
        ];

        $this->rulesFlashMessage = [
            'message' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
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
    private function validatorLogin(array $requiredKeys, array $validateData): void
    {
        foreach ($requiredKeys as $key => $rule) {

            if (!array_key_exists($key, $validateData)) {
                throw new InvalidArgumentException("$key がありません");
            }

            if (!$rule['validator']($validateData[$key])) {
                throw new RuntimeException("$key : {$rule['message']}");
            }
        }
    }
    // ----- $_SESSION['customer']['order']['total_price'] -----
    // in < customer/top/cart/confirm.php >
    // out< customer/top/delivery/done.php >
    public function validateTotalPrice(?int $price): void
    {
        $validatData = ['total_price' => $price];
        $this->validator($this->rulesTotalPrice, $validatData);
    }

    // ----- $_SESSION['customer']['cart'] -----
    // in < Customer/Service/CustomerTop.php >
    // out< customer/top/cart/enter.php >
    // out< customer/top/cart/confirm.php >
    // out< customer/top/delivery/confirm.php >
    // out< customer/top/delivery/done.php >
    public function validateCartIdQuantity(?array $cart): void
    {
        if (is_null($cart) || empty($cart)) {
            throw new InvalidArgumentException("cartの中身がありません");
        }

        foreach ($cart as $productId => $content) {
            $validatData = ['id' => $productId, 'quantity' => $content['quantity']];
            $this->validator($this->rulesCartIdQuantity, $validatData);
        }
    }

    // ----- $_SESSION['customer']['order']['order_id'] -----
    // in < customer/top/delivery/done.php >
    // out< customer/top/delivery/complete.php >
    public function validateOrderId(?int $id): void
    {
        $validatData = ['id' => $id];
        $this->validator($this->rulesOrderId, $validatData);
    }

    // ----- $_SESSION['customer']['login']['is_login'] -----
    public function validateIsLogin(?bool $data): void
    {
        $validatData = ['is_login' => $data];
        $this->validatorLogin($this->rulesIsLogin, $validatData);
    }

    // ----- $_SESSION['customer']['login']['id'] -----
    // ----- $_SESSION['customer']['login']['email'] -----
    public function validateLoginIdEmail(?int $id, ?string $email): void
    {
        $validatData = ['login_id' => $id, 'login_email' => $email];
        $this->validatorLogin($this->rulesLoginIdEmail, $validatData);
    }

    // ----- $_SESSION['flash']['success'] -----
    public function validateFlashMessage(?string $message): void
    {
        $validatData = ['message' => $message];
        $this->validator($this->rulesFlashMessage, $validatData);
    }
}

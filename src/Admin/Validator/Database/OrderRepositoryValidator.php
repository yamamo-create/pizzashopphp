<?php

namespace App\Admin\Validator\Database;

require_once __DIR__ . '/../../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class OrderRepositoryValidator
{
    private RegexCheck $reg;

    // order
    private readonly array $rulesOrderRepositoryfindCurrentCustomerOrder;
    private readonly array $rulesOrderRepositoryFindCompletCustomerOrder;
    private readonly array $rulesOrderRepositoryFindOrderPlusCustomer;
    private readonly array $rulesOrderRepositoryFindOrderPlusOrderitem;
    private readonly array $rulesOrderRepositoryFindCompleteOrderPlusCustomer;
    private readonly array $rulesOrderRepositoryFindCompleteOrderPlusOrderitem;
    private readonly array $rulesOrderRepositoryFindCancelOrderPlusCustomer;
    private readonly array $rulesOrderRepositoryFindCancelOrderPlusOrderitem;

    public function __construct()
    {
        $this->reg = new RegexCheck();

        // ----- order -----
        $this->rulesOrderRepositoryfindCurrentCustomerOrder = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [0, 1, 2, 3, 4, 9], true), 'message' => '0、1、2、3、4、9である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_name' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
        ];

        $this->rulesOrderRepositoryFindCompletCustomerOrder = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [4], true), 'message' => '0、1、2、3、4、9である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_name' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
        ];

        $this->rulesOrderRepositoryFindOrderPlusCustomer = [
            'order_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'customer_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [1, 2, 3], true), 'message' => '1, 2, 3である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false || $this->reg->deletedEmail($v), 'message' => 'メールアドレスの形式ではありません'],
            'lastname' => ['validator' => fn($v) => $this->reg->fullchar($v) || $this->reg->deletedCustomer($v), 'message' => '苗字が全角文字ではありません'],
            'firstname' => ['validator' => fn($v) => $this->reg->fullchar($v) || $this->reg->deletedCustomer($v), 'message' => '名前が全角文字ではありません'],
            'phone' => ['validator' => fn($v) => $this->reg->phone($v) || $this->reg->deletedCustomer($v), 'message' => '電話番号の形式ではありません'],
            'post' => ['validator' => fn($v) => $this->reg->postnumber($v) || $this->reg->deletedCustomer($v), 'message' => '郵便番号の形式ではありません'],
            'address' => ['validator' => fn($v) => $this->reg->address($v) || $this->reg->deletedCustomer($v), 'message' => '住所の形式ではありません'],
        ];

        $this->rulesOrderRepositoryFindOrderPlusOrderitem = [
            'order_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'customer_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [1, 2, 3], true), 'message' => '1, 2, 3である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_name' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
        ];

        $this->rulesOrderRepositoryFindCompleteOrderPlusCustomer = [
            'order_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'customer_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [4], true), 'message' => '4である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false || $this->reg->deletedEmail($v), 'message' => 'メールアドレスの形式ではありません'],
            'lastname' => ['validator' => fn($v) => $this->reg->fullchar($v) || $this->reg->deletedCustomer($v), 'message' => '苗字が全角文字ではありません'],
            'firstname' => ['validator' => fn($v) => $this->reg->fullchar($v) || $this->reg->deletedCustomer($v), 'message' => '名前が全角文字ではありません'],
            'phone' => ['validator' => fn($v) => $this->reg->phone($v) || $this->reg->deletedCustomer($v), 'message' => '電話番号の形式ではありません'],
            'post' => ['validator' => fn($v) => $this->reg->postnumber($v) || $this->reg->deletedCustomer($v), 'message' => '郵便番号の形式ではありません'],
            'address' => ['validator' => fn($v) => $this->reg->address($v) || $this->reg->deletedCustomer($v), 'message' => '住所の形式ではありません'],
        ];

        $this->rulesOrderRepositoryFindCompleteOrderPlusOrderitem = [
            'order_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'customer_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [4], true), 'message' => '4である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_name' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'product_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'product_quantity' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
        ];

        $this->rulesOrderRepositoryFindCancelOrderPlusCustomer = [
            'order_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'customer_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [9], true), 'message' => '9である必要があります'],
            'total_price' => ['validator' => fn($v) => ctype_digit((string)$v), 'message' => '正の整数ではありません'],
            'created_at' => ['validator' => fn($v) => is_string($v), 'message' => '文字列ではありません'],
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false || $this->reg->deletedEmail($v), 'message' => 'メールアドレスの形式ではありません'],
            'lastname' => ['validator' => fn($v) => $this->reg->fullchar($v) || $this->reg->deletedCustomer($v), 'message' => '苗字が全角文字ではありません'],
            'firstname' => ['validator' => fn($v) => $this->reg->fullchar($v) || $this->reg->deletedCustomer($v), 'message' => '名前が全角文字ではありません'],
            'phone' => ['validator' => fn($v) => $this->reg->phone($v) || $this->reg->deletedCustomer($v), 'message' => '電話番号の形式ではありません'],
            'post' => ['validator' => fn($v) => $this->reg->postnumber($v) || $this->reg->deletedCustomer($v), 'message' => '郵便番号の形式ではありません'],
            'address' => ['validator' => fn($v) => $this->reg->address($v) || $this->reg->deletedCustomer($v), 'message' => '住所の形式ではありません'],
        ];

        $this->rulesOrderRepositoryFindCancelOrderPlusOrderitem = [
            'order_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'customer_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => '自然数ではありません'],
            'status' => ['validator' => fn($v) => in_array($v, [9], true), 'message' => '9である必要があります'],
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
    public function validateOrderRepositoryfindCurrentCustomerOrder(?array $orderData): void
    {
        if (empty($orderData)) {
            return;
        }
        foreach ($orderData as $data) {
            $this->validator($this->rulesOrderRepositoryfindCurrentCustomerOrder, $data);
        }
    }

    public function validateOrderRepositoryFindCompletCustomerOrder(?array $orderData): void
    {
        if (empty($orderData)) {
            return;
        }
        foreach ($orderData as $data) {
            $this->validator($this->rulesOrderRepositoryFindCompletCustomerOrder, $data);
        }
    }

    public function validateOrderRepositoryFindOrderPlusCustomer(?array $orderData): void
    {
        if (empty($orderData)) {
            return;
        }
        foreach ($orderData as $data) {
            $this->validator($this->rulesOrderRepositoryFindOrderPlusCustomer, $data);
        }
    }

    public function validateOrderRepositoryFindOrderPlusOrderitem(?array $ordeitemrData): void
    {
        if (empty($ordeitemrData)) {
            return;
        }
        foreach ($ordeitemrData as $data) {
            $this->validator($this->rulesOrderRepositoryFindOrderPlusOrderitem, $data);
        }
    }

    public function validateOrderRepositoryFindCompleteOrderPlusCustomer(?array $orderData): void
    {
        if (empty($orderData)) {
            return;
        }
        foreach ($orderData as $data) {
            $this->validator($this->rulesOrderRepositoryFindCompleteOrderPlusCustomer, $data);
        }
    }

    public function validateOrderRepositoryFindCompleteOrderPlusOrderitem(?array $ordeitemrData): void
    {
        if (empty($ordeitemrData)) {
            return;
        }
        foreach ($ordeitemrData as $data) {
            $this->validator($this->rulesOrderRepositoryFindCompleteOrderPlusOrderitem, $data);
        }
    }

    public function validateOrderRepositoryFindCancelOrderPlusCustomer(?array $orderData): void
    {
        if (empty($orderData)) {
            return;
        }
        foreach ($orderData as $data) {
            $this->validator($this->rulesOrderRepositoryFindCancelOrderPlusCustomer, $data);
        }
    }

    public function validateOrderRepositoryFindCancelOrderPlusOrderitem(?array $ordeitemrData): void
    {
        if (empty($ordeitemrData)) {
            return;
        }
        foreach ($ordeitemrData as $data) {
            $this->validator($this->rulesOrderRepositoryFindCancelOrderPlusOrderitem, $data);
        }
    }
}

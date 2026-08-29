<?php

namespace App\Admin\Validator;

require_once __DIR__ . '/../../Config/Path.php';
require_once SRC_PATH . '/Common/RegexCheck.php';

use App\Common\RegexCheck;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AdminSessionValidator
{
    private RegexCheck $reg;

    private readonly array $rulesIsLogin;
    private readonly array $rulesLoginIdEmail;
    private readonly array $rulesSystemChoiceId;
    private readonly array $rulesDeleteIdEmail;
    private readonly array $rulesPagecreatePuroductId;
    private readonly array $rulesManagerCustomerId;
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

        $this->rulesSystemChoiceId = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => 'Session is Broken'],
        ];

        $this->rulesDeleteIdEmail = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => 'Session is Broken'],
            'email' => ['validator' => fn($v) => filter_var($v, FILTER_VALIDATE_EMAIL) !== false, 'message' => 'Session is Broken'],
        ];

        $this->rulesPagecreatePuroductId = [
            'id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => 'Session is Broken'],
        ];

        $this->rulesManagerCustomerId = [
            'choice_id' => ['validator' => fn($v) => ctype_digit((string)$v) && (int)$v > 0, 'message' => 'Session is Broken'],
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

    // ----- $_SESSION['admin']['system']['choice_id'] -----
    // in < admin/manage/staff/branch.php >
    // out< admin/manage/staff/read/enter.php >
    // out< admin/manage/staff/delete/enter.php >

    // in < admin/manage/product/branch.php >
    // out< admin/manage/product/read/enter.php >
    // out< admin/manage/product/update/enter.php >
    // out< admin/manage/product/update/check_done.php >
    // out< admin/manage/product/delete/enter.php >
    // out< admin/manage/product/delete/check_done.php >
    public function validateSystemChoiceId(?int $choiceId): void
    {
        $validatData = ['choice_id' => $choiceId];
        $this->validator($this->rulesSystemChoiceId, $validatData);
    }

    // ----- $_SESSION['admin']['form']['delete']['id'] -----
    // ----- $_SESSION['admin']['form']['delete']['email'] -----
    // in < admin/manage/staff/delete/enter.php >
    // out< admin/manage/staff/delete/check_done.php >
    public function validateDeleteIdEmail(?int $id, ?string $email): void
    {
        $validatData = ['id' => $id, 'email' => $email];
        $this->validator($this->rulesDeleteIdEmail, $validatData);
    }

    // ----- $_SESSION['admin']['form']['page']['meal'] -----
    // ----- $_SESSION['admin']['form']['page']['dessert'] -----
    // in < admin/manage/pagecreate/enter.php >
    // out< admin/manage/pagecreate/confirm.php >
    public function validatePagecreatePuroductId(?array $productIds): void
    {
        if (empty($productIds)) {
            throw new InvalidArgumentException("Session is Broken");
        }
        foreach ($productIds as $id) {
            $validatData = ['id' => $id];
            $this->validator($this->rulesPagecreatePuroductId, $validatData);
        }
    }

    // ----- $_SESSION -----
    // in < admin/manage/customer/enter.php >
    // out< admin/manage/customer/confirm.php >
    // out< admin/manage/customer/done.php >
    public function validateCustomerChoiceId(?int $choiceId): void
    {
        $validatData = ['choice_id' => $choiceId];
        $this->validator($this->rulesManagerCustomerId, $validatData);
    }

    // ----- $_SESSION['admin']['login']['is_login'] -----
    public function validateIsLogin(?bool $data): void
    {
        $validatData = ['is_login' => $data];
        $this->validatorLogin($this->rulesIsLogin, $validatData);
    }

    // ----- $_SESSION['admin']['login']['id'] -----
    // ----- $_SESSION['admin']['login']['email'] -----
    public function validateLoginIdEmail(?int $id, ?string $email): void
    {
        $validatData = ['login_id' => $id, 'login_email' => $email];
        $this->validatorLogin($this->rulesLoginIdEmail, $validatData);
    }

    //
    // < 共通 >
    //

    // ----- $_SESSION['flash']['success'] -----
    public function validateFlashMessage(?string $message): void
    {
        $validatData = ['message' => $message];
        $this->validator($this->rulesFlashMessage, $validatData);
    }
}

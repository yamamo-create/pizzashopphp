<?php

namespace App\Customer\Token;

class CustomerOneTimeToken
{
    public static function generate(string $key): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['customer']['one_time_token'][$key] = $token;
        return $token;
    }
    public static function validateAndDestroy(string $key, ?string $postToken): bool
    {
        $result = self::validate($key, $postToken);
        if ($result) {
            self::destroy($key);
        }
        return $result;
    }
    public static function isTimeout(string $key): bool
    {
        return !isset($_SESSION['customer']['one_time_token'][$key]);
    }

    // 内部処理
    private static function validate(string $key, ?string $postToken): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $sessionToken = $_SESSION['customer']['one_time_token'][$key] ?? null;

        if (!is_string($postToken) || !is_string($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $postToken);
    }
    private static function destroy(string $key): void
    {
        unset($_SESSION['customer']['one_time_token'][$key]);
    }
}

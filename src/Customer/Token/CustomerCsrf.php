<?php

namespace App\Customer\Token;

class CustomerCsrf
{
    public static function regenerate(): void
    {
        $_SESSION['customer']['csrf'] = bin2hex(random_bytes(32));
    }
    public static function ensure(): string
    {
        if (!isset($_SESSION['customer']['csrf'])) {
            $_SESSION['customer']['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['customer']['csrf'];
    }
    public static function isTimeout(): bool
    {
        return !isset($_SESSION['customer']['csrf']) || !is_string($_SESSION['customer']['csrf']);
    }
    public static function validate(?string $postToken): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $sessionToken = $_SESSION['customer']['csrf'] ?? null;

        if (!is_string($postToken) || !is_string($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $postToken);
    }
}

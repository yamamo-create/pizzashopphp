<?php

namespace App\Admin\Token;

class AdminCsrf
{
    public static function regenerate(): void
    {
        $_SESSION['admin']['csrf'] = bin2hex(random_bytes(32));
    }
    public static function ensure(): string
    {
        if (!isset($_SESSION['admin']['csrf'])) {
            $_SESSION['admin']['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['admin']['csrf'];
    }
    public static function isTimeout(): bool
    {
        return !isset($_SESSION['admin']['csrf']) || !is_string($_SESSION['admin']['csrf']);
    }
    public static function validate(?string $postToken): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $sessionToken = $_SESSION['admin']['csrf'] ?? null;

        if (!is_string($postToken) || !is_string($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $postToken);
    }
}
